<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class UpdateCurrencyRates extends Command
{
    protected $signature = 'currency:update';

    protected $description = 'Refresh cached currency exchange rates.';

    public function handle(CurrencyService $currencyService): int
    {
        $rates = $currencyService->refreshRates();

        $this->info('Currency rates refreshed.');
        $this->line('Rates loaded: '.count($rates));

        return self::SUCCESS;
    }
}
