<?php

namespace App\Services;

use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\User;
use App\Notifications\LoyaltyPointsEarnedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoyaltyService
{
    public const POINTS_PER_USD = 10;

    public const POINTS_PER_DISCOUNT_USD = 100;

    public const MIN_REDEEM_POINTS = 500;

    public const REGISTER_BONUS_POINTS = 100;

    public const REVIEW_BONUS_POINTS = 50;

    public function earnPoints(User $user, Order $order): LoyaltyTransaction
    {
        $points = (int) floor((float) $order->total_usd * self::POINTS_PER_USD);
        $reference = "order:{$order->id}:earned";

        return $this->addPoints($user, $points, 'earn', 'Order reward points', $reference, $order);
    }

    public function redeemPoints(User $user, int $points, ?Order $order = null): float
    {
        $points = $this->normalizedRedeemPoints($points);

        if ($points === 0) {
            return 0.0;
        }

        return DB::transaction(function () use ($user, $points, $order) {
            $reference = $order ? "order:{$order->id}:redeem" : null;

            if ($reference && $existing = $this->transactionByReference($reference)) {
                return round(abs($existing->points) / self::POINTS_PER_DISCOUNT_USD, 2);
            }

            $wallet = $this->lockedWallet($user);
            $availablePoints = $this->availableBalance($user);
            $redeemablePoints = min($points, $availablePoints);

            if ($redeemablePoints < self::MIN_REDEEM_POINTS) {
                return 0.0;
            }

            $wallet->decrement('balance', $redeemablePoints);

            LoyaltyTransaction::query()->create([
                'user_id' => $user->id,
                'points' => -abs($redeemablePoints),
                'type' => 'redeem',
                'description' => 'Redeemed loyalty points',
                'order_id' => $order?->id,
                'reference' => $reference,
            ]);

            return round($redeemablePoints / self::POINTS_PER_DISCOUNT_USD, 2);
        });
    }

    public function addBonusPoints(User $user, string $reason, ?int $points = null, ?Order $order = null): LoyaltyTransaction
    {
        $bonusPoints = $points ?? match (true) {
            $reason === 'register' => self::REGISTER_BONUS_POINTS,
            str_starts_with($reason, 'review:') => self::REVIEW_BONUS_POINTS,
            $reason === 'review' => self::REVIEW_BONUS_POINTS,
            $reason === 'birthday' => 250,
            default => 0,
        };
        $reference = $this->bonusReference($user, $reason);
        $description = match (true) {
            $reason === 'register' => 'Welcome bonus points',
            str_starts_with($reason, 'review:') || $reason === 'review' => 'Review bonus points',
            default => "Bonus points: {$reason}",
        };

        return $this->addPoints($user, $bonusPoints, 'bonus', $description, $reference, $order);
    }

    public function previewRedemption(User $user, int $points, float $orderTotalUsd): array
    {
        $requestedPoints = max(0, $points);
        $normalizedPoints = $this->normalizedRedeemPoints($requestedPoints);
        $availablePoints = $this->availableBalance($user);
        $maxByTotal = (int) floor(max(0, $orderTotalUsd) * self::POINTS_PER_DISCOUNT_USD);
        $redeemablePoints = min($normalizedPoints, $availablePoints, $maxByTotal);

        if ($redeemablePoints < self::MIN_REDEEM_POINTS) {
            $redeemablePoints = 0;
        }

        return [
            'requested_points' => $requestedPoints,
            'redeemable_points' => $redeemablePoints,
            'discount_usd' => round($redeemablePoints / self::POINTS_PER_DISCOUNT_USD, 2),
            'available_points' => $availablePoints,
            'minimum_points' => self::MIN_REDEEM_POINTS,
            'points_per_discount_usd' => self::POINTS_PER_DISCOUNT_USD,
        ];
    }

    public function expirePoints(): int
    {
        $expired = 0;

        LoyaltyTransaction::query()
            ->whereIn('type', ['earn', 'bonus'])
            ->where('points', '>', 0)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->orderBy('id')
            ->chunkById(100, function (Collection $transactions) use (&$expired) {
                foreach ($transactions as $transaction) {
                    $reference = $transaction->reference ?: "transaction:{$transaction->id}";
                    $expired += $this->expireTransaction($transaction, "{$reference}:expired");
                }
            });

        return $expired;
    }

    public function availableBalance(User $user): int
    {
        return max(0, (int) $this->wallet($user)->balance);
    }

    private function addPoints(User $user, int $points, string $type, string $description, string $reference, ?Order $order = null): LoyaltyTransaction
    {
        if ($points <= 0) {
            throw ValidationException::withMessages(['points' => 'Loyalty points must be greater than zero.']);
        }

        return DB::transaction(function () use ($user, $points, $type, $description, $reference, $order) {
            if ($existing = $this->transactionByReference($reference)) {
                return $existing;
            }

            $wallet = $this->lockedWallet($user);
            $wallet->increment('balance', $points);

            $transaction = LoyaltyTransaction::query()->create([
                'user_id' => $user->id,
                'points' => $points,
                'type' => $type,
                'description' => $description,
                'order_id' => $order?->id,
                'reference' => $reference,
                'expires_at' => now()->addYear(),
            ]);

            $user->notify(new LoyaltyPointsEarnedNotification($transaction, [
                'points' => $points,
                'total_points' => $wallet->fresh()->balance,
                'loyalty_url' => route('account.loyalty'),
            ]));

            return $transaction;
        });
    }

    private function expireTransaction(LoyaltyTransaction $transaction, string $reference): int
    {
        return DB::transaction(function () use ($transaction, $reference) {
            if ($this->transactionByReference($reference)) {
                return 0;
            }

            $wallet = $this->lockedWallet($transaction->user);
            $pointsToExpire = min((int) $transaction->points, (int) $wallet->balance);

            if ($pointsToExpire <= 0) {
                return 0;
            }

            $wallet->decrement('balance', $pointsToExpire);

            LoyaltyTransaction::query()->create([
                'user_id' => $transaction->user_id,
                'points' => -$pointsToExpire,
                'type' => 'expired',
                'description' => 'Expired loyalty points',
                'order_id' => $transaction->order_id,
                'reference' => $reference,
            ]);

            return $pointsToExpire;
        });
    }

    private function normalizedRedeemPoints(int $points): int
    {
        if ($points < self::MIN_REDEEM_POINTS) {
            return 0;
        }

        return intdiv($points, self::POINTS_PER_DISCOUNT_USD) * self::POINTS_PER_DISCOUNT_USD;
    }

    private function bonusReference(User $user, string $reason): string
    {
        if ($reason === 'register') {
            return "user:{$user->id}:register_bonus";
        }

        if (str_starts_with($reason, 'review:')) {
            return 'review:'.str($reason)->after('review:')->toString().':bonus';
        }

        return "user:{$user->id}:bonus:".str($reason)->slug('_')->toString();
    }

    private function transactionByReference(string $reference): ?LoyaltyTransaction
    {
        return LoyaltyTransaction::query()->where('reference', $reference)->first();
    }

    private function wallet(User $user): LoyaltyPoint
    {
        return LoyaltyPoint::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0],
        );
    }

    private function lockedWallet(User $user): LoyaltyPoint
    {
        $this->wallet($user);

        return LoyaltyPoint::query()
            ->where('user_id', $user->id)
            ->lockForUpdate()
            ->firstOrFail();
    }
}
