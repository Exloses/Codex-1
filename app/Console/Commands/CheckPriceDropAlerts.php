<?php

namespace App\Console\Commands;

use App\Models\StockNotification;
use App\Notifications\PriceDropNotification;
use App\Services\ProductAlertService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckPriceDropAlerts extends Command
{
    protected $signature = 'notifications:check-price-drops';

    protected $description = 'Send price drop alerts when products reach the requested target price.';

    public function handle(ProductAlertService $alerts): int
    {
        $checked = 0;
        $sent = 0;
        $skipped = 0;

        StockNotification::query()
            ->with(['product', 'productVariant', 'user'])
            ->priceAlerts()
            ->pending()
            ->whereNotNull('target_price_usd')
            ->chunkById(100, function ($notifications) use ($alerts, &$checked, &$sent, &$skipped) {
                foreach ($notifications as $priceAlert) {
                    $checked++;
                    $currentPrice = $alerts->notificationCurrentPrice($priceAlert);

                    if ($currentPrice === null || $currentPrice > (float) $priceAlert->target_price_usd) {
                        $skipped++;

                        continue;
                    }

                    $email = $alerts->recipientEmail($priceAlert);

                    if (! $email) {
                        $skipped++;

                        continue;
                    }

                    try {
                        $notification = new PriceDropNotification($priceAlert->product, [
                            'product' => $priceAlert->product,
                            'name' => $priceAlert->product->name,
                            'old_price_usd' => $priceAlert->target_price_usd,
                            'new_price_usd' => $currentPrice,
                        ]);

                        if ($priceAlert->user) {
                            $priceAlert->user->notify($notification);
                        } else {
                            Notification::route('mail', $email)->notify($notification);
                        }

                        $priceAlert->forceFill(['is_notified' => true])->save();
                        $sent++;
                    } catch (\Throwable $exception) {
                        $skipped++;

                        report($exception);
                    }
                }
            });

        $this->info("Checked {$checked} price alert(s); sent {$sent}; skipped {$skipped}.");

        return self::SUCCESS;
    }
}
