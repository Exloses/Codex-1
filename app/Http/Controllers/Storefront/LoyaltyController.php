<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyTransaction;
use App\Services\LoyaltyService;
use Inertia\Inertia;
use Inertia\Response;

class LoyaltyController extends Controller
{
    public function index(LoyaltyService $loyaltyService): Response
    {
        $user = auth()->user();
        $wallet = $user->loyaltyPoint()->firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        return Inertia::render('Account/LoyaltyPoints', [
            'summary' => [
                'balance' => $loyaltyService->availableBalance($user),
                'minimum_points' => LoyaltyService::MIN_REDEEM_POINTS,
                'points_per_usd' => LoyaltyService::POINTS_PER_USD,
                'points_per_discount_usd' => LoyaltyService::POINTS_PER_DISCOUNT_USD,
                'register_bonus_points' => LoyaltyService::REGISTER_BONUS_POINTS,
                'review_bonus_points' => LoyaltyService::REVIEW_BONUS_POINTS,
            ],
            'loyaltyPoint' => [
                'id' => $wallet->id,
                'balance' => $wallet->balance,
            ],
            'transactions' => LoyaltyTransaction::query()
                ->select(['id', 'user_id', 'points', 'type', 'description', 'order_id', 'reference', 'expires_at', 'created_at'])
                ->where('user_id', $user->id)
                ->with('order:id,user_id,order_number,total_usd,status')
                ->latest()
                ->paginate(20)
                ->through(fn (LoyaltyTransaction $transaction) => [
                    'id' => $transaction->id,
                    'points' => $transaction->points,
                    'type' => $transaction->type,
                    'description' => $transaction->description,
                    'reference' => $transaction->reference,
                    'expires_at' => $transaction->expires_at?->toDateString(),
                    'created_at' => $transaction->created_at?->toDateString(),
                    'order' => $transaction->order ? [
                        'id' => $transaction->order->id,
                        'order_number' => $transaction->order->order_number,
                        'status' => $transaction->order->status,
                    ] : null,
                ]),
        ]);
    }
}
