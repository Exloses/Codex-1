<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('currency:update')->hourly();
Schedule::command('affiliate:release-commissions')->hourly();
Schedule::command('orders:cancel-unpaid')->everyThirtyMinutes();
Schedule::command('notifications:check-stock')->everyThirtyMinutes();
Schedule::command('notifications:check-price-drops')->hourly();
Schedule::command('affiliate:check-tiers')->daily();
