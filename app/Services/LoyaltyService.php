<?php

namespace App\Services;

use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    public const POINTS_PER_USD = 10;

    public const POINTS_PER_DISCOUNT_USD = 100;

    public function earnPoints(User $user, Order $order): LoyaltyTransaction
    {
        $points = (int) floor((float) $order->total_usd * self::POINTS_PER_USD);

        return $this->addPoints($user, $points, 'earn', 'Order reward points', $order);
    }

    public function redeemPoints(User $user, int $points, ?Order $order = null): float
    {
        return DB::transaction(function () use ($user, $points, $order) {
            $wallet = $this->wallet($user);

            if ($points < self::POINTS_PER_DISCOUNT_USD || $wallet->balance < $points) {
                return 0.0;
            }

            $wallet->decrement('balance', $points);

            LoyaltyTransaction::query()->create([
                'user_id' => $user->id,
                'points' => -abs($points),
                'type' => 'redeem',
                'description' => 'Redeemed loyalty points',
                'order_id' => $order?->id,
            ]);

            return round($points / self::POINTS_PER_DISCOUNT_USD, 2);
        });
    }

    public function addBonusPoints(User $user, string $reason, ?int $points = null): LoyaltyTransaction
    {
        $bonusPoints = $points ?? match ($reason) {
            'register' => 100,
            'review' => 50,
            'birthday' => 250,
            default => 0,
        };

        return $this->addPoints($user, $bonusPoints, 'bonus', "Bonus points: {$reason}");
    }

    private function addPoints(User $user, int $points, string $type, string $description, ?Order $order = null): LoyaltyTransaction
    {
        return DB::transaction(function () use ($user, $points, $type, $description, $order) {
            $wallet = $this->wallet($user);
            $wallet->increment('balance', $points);

            return LoyaltyTransaction::query()->create([
                'user_id' => $user->id,
                'points' => $points,
                'type' => $type,
                'description' => $description,
                'order_id' => $order?->id,
                'expires_at' => now()->addYear(),
            ]);
        });
    }

    private function wallet(User $user): LoyaltyPoint
    {
        return LoyaltyPoint::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0],
        );
    }
}
