<?php

namespace App\Console\Commands;

use App\Models\StockNotification;
use App\Notifications\StockAvailableNotification;
use App\Services\ProductAlertService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckStockNotifications extends Command
{
    protected $signature = 'notifications:check-stock';

    protected $description = 'Send stock availability notifications for products back in stock.';

    public function handle(ProductAlertService $alerts): int
    {
        $checked = 0;
        $sent = 0;
        $skipped = 0;

        StockNotification::query()
            ->with(['product', 'productVariant', 'user'])
            ->stockAlerts()
            ->pending()
            ->chunkById(100, function ($notifications) use ($alerts, &$checked, &$sent, &$skipped) {
                foreach ($notifications as $stockNotification) {
                    $checked++;

                    if (! $alerts->notificationIsInStock($stockNotification)) {
                        $skipped++;

                        continue;
                    }

                    $email = $alerts->recipientEmail($stockNotification);

                    if (! $email) {
                        $skipped++;

                        continue;
                    }

                    try {
                        $notification = new StockAvailableNotification($stockNotification->product, [
                            'product' => $stockNotification->product,
                            'name' => $stockNotification->product->name,
                        ]);

                        if ($stockNotification->user) {
                            $stockNotification->user->notify($notification);
                        } else {
                            Notification::route('mail', $email)->notify($notification);
                        }

                        $stockNotification->forceFill(['is_notified' => true])->save();
                        $sent++;
                    } catch (\Throwable $exception) {
                        $skipped++;

                        report($exception);
                    }
                }
            });

        $this->info("Checked {$checked} stock alert(s); sent {$sent}; skipped {$skipped}.");

        return self::SUCCESS;
    }
}
