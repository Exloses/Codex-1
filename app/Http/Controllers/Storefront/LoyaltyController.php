<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyTransaction;
use Inertia\Inertia;
use Inertia\Response;

class LoyaltyController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        return Inertia::render('Account/LoyaltyPoints', [
            'loyaltyPoint' => $user->loyaltyPoint()->firstOrCreate(['user_id' => $user->id], ['balance' => 0]),
            'transactions' => LoyaltyTransaction::query()
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(20),
        ]);
    }
}
