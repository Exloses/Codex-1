<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function getRates(Request $request, CurrencyService $currencyService)
    {
        $amount = (float) $request->input('amount', 1);
        $currency = strtoupper($request->input('currency', 'USD'));

        return $this->ok([
            'base' => 'USD',
            'rates' => $currencyService->getRates(),
            'converted' => $currencyService->convert($amount, $currency),
        ]);
    }
}
