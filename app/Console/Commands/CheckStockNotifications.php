<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\StockNotification;
use Illuminate\Console\Command;

class CheckStockNotifications extends Command
{
    protected $signature = 'notifications:check-stock';

    protected $description = 'Send stock availability notifications for products back in stock.';

    public function handle(): int
    {
        $sent = 0;

        StockNotification::query()
            ->with(['product', 'productVariant', 'user'])
            ->where('type', 'stock')
            ->where('is_notified', false)
            ->chunkById(100, function ($notifications) use (&$sent) {
                foreach ($notifications as $notification) {
                    if (! $this->isInStock($notification)) {
                        continue;
                    }

                    $email = $notification->user?->email ?? $notification->guest_email;

                    if (! $email) {
                        continue;
                    }

                    SendEmailJob::dispatch(
                        $email,
                        'Product is back in stock',
                        "{$notification->product?->name} is available again.",
                    );

                    $notification->forceFill(['is_notified' => true])->save();
                    $sent++;
                }
            });

        $this->info("Queued {$sent} stock notification email(s).");

        return self::SUCCESS;
    }

    private function isInStock(StockNotification $notification): bool
    {
        if ($notification->productVariant) {
            return $notification->productVariant->stock > 0;
        }

        return $notification->product?->stock > 0;
    }
}
