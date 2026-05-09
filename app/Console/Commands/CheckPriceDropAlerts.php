<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\StockNotification;
use Illuminate\Console\Command;

class CheckPriceDropAlerts extends Command
{
    protected $signature = 'notifications:check-price-drops';

    protected $description = 'Send price drop alerts when products reach the requested target price.';

    public function handle(): int
    {
        $sent = 0;

        StockNotification::query()
            ->with(['product', 'productVariant', 'user'])
            ->where('type', 'price')
            ->where('is_notified', false)
            ->whereNotNull('target_price_usd')
            ->chunkById(100, function ($notifications) use (&$sent) {
                foreach ($notifications as $notification) {
                    $currentPrice = $this->currentPrice($notification);

                    if ($currentPrice === null || $currentPrice > (float) $notification->target_price_usd) {
                        continue;
                    }

                    $email = $notification->user?->email ?? $notification->guest_email;

                    if (! $email) {
                        continue;
                    }

                    SendEmailJob::dispatch(
                        $email,
                        'Price drop alert',
                        "{$notification->product?->name} reached your target price of {$notification->target_price_usd} USD.",
                    );

                    $notification->forceFill(['is_notified' => true])->save();
                    $sent++;
                }
            });

        $this->info("Queued {$sent} price drop alert email(s).");

        return self::SUCCESS;
    }

    private function currentPrice(StockNotification $notification): ?float
    {
        if ($notification->productVariant?->price !== null) {
            return (float) $notification->productVariant->price;
        }

        return $notification->product ? (float) $notification->product->selling_price : null;
    }
}
