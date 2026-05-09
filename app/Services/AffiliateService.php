<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AffiliateService
{
    private const TIER_RULES = [
        'starter' => ['minimum_sales' => 0, 'rate' => 5.00],
        'silver' => ['minimum_sales' => 10, 'rate' => 7.50],
        'gold' => ['minimum_sales' => 50, 'rate' => 10.00],
        'platinum' => ['minimum_sales' => 100, 'rate' => 12.50],
    ];

    public function register(User $user): Affiliate
    {
        return DB::transaction(function () use ($user) {
            $code = $this->generateReferralCode($user);

            return Affiliate::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'referral_code' => $code,
                    'referral_link' => url('/ref/'.$code),
                    'tier' => 'starter',
                    'commission_rate' => self::TIER_RULES['starter']['rate'],
                    'is_active' => true,
                ]
            );
        });
    }

    public function processCommission(Order $order): ?AffiliateCommission
    {
        if (! $order->affiliate_code) {
            return null;
        }

        $affiliate = Affiliate::query()
            ->where('referral_code', $order->affiliate_code)
            ->where('is_active', true)
            ->first();

        if (! $affiliate) {
            return null;
        }

        return DB::transaction(function () use ($affiliate, $order) {
            $commission = AffiliateCommission::query()->firstOrCreate(
                ['affiliate_id' => $affiliate->id, 'order_id' => $order->id],
                [
                    'order_total_usd' => $order->total_usd,
                    'commission_rate' => $affiliate->commission_rate,
                    'commission_usd' => round((float) $order->total_usd * ((float) $affiliate->commission_rate / 100), 2),
                    'status' => 'pending',
                    'available_at' => now()->addDays(7),
                ]
            );

            if ($commission->wasRecentlyCreated) {
                $affiliate->increment('total_sales');
                $affiliate->increment('total_referrals');
                $affiliate->increment('total_earned_usd', (float) $commission->commission_usd);
                $this->checkAndUpgradeTier($affiliate->refresh());
            }

            return $commission;
        });
    }

    public function checkAndUpgradeTier(Affiliate $affiliate): Affiliate
    {
        $tier = collect(self::TIER_RULES)
            ->filter(fn (array $rule) => $affiliate->total_sales >= $rule['minimum_sales'])
            ->keys()
            ->last() ?? 'starter';

        $affiliate->forceFill([
            'tier' => $tier,
            'commission_rate' => self::TIER_RULES[$tier]['rate'],
        ])->save();

        return $affiliate;
    }

    private function generateReferralCode(User $user): string
    {
        do {
            $code = Str::upper(Str::slug(Str::limit($user->name, 8, '')).Str::random(6));
        } while (Affiliate::query()->where('referral_code', $code)->exists());

        return $code;
    }
}
