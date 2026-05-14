<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use App\Services\StorefrontCache;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function getRates(Request $request, CurrencyService $currencyService)
    {
        $amount = (float) $request->input('amount', 1);
        $currency = strtoupper($request->input('currency', 'USD'));

        return $this->ok(StorefrontCache::remember(
            StorefrontCache::currencyKey($amount, $currency),
            StorefrontCache::CURRENCY_TTL,
            fn () => [
                'base' => 'USD',
                'rates' => $currencyService->getRates(),
                'converted' => $currencyService->convert($amount, $currency),
            ]
        ));
    }
}
