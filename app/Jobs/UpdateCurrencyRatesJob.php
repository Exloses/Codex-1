<?php

namespace App\Jobs;

use App\Services\CurrencyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateCurrencyRatesJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function handle(CurrencyService $currencyService): void
    {
        $currencyService->refreshRates();
    }
}
