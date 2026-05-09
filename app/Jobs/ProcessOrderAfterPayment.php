<?php

namespace App\Jobs;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\AffiliateService;
use App\Services\DropshipService;
use App\Services\LoyaltyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ProcessOrderAfterPayment implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public Order $order,
    ) {}

    public function handle(
        DropshipService $dropshipService,
        AffiliateService $affiliateService,
        LoyaltyService $loyaltyService,
    ): void {
        $this->order->loadMissing('items.product', 'items.productVariant', 'user');

        DB::transaction(function () use ($dropshipService, $affiliateService, $loyaltyService) {
            $this->reduceStockAndRecordSales();

            $dropshipService->createDropshipOrders($this->order);
            $affiliateService->processCommission($this->order);

            if ($this->order->user) {
                $loyaltyService->earnPoints($this->order->user, $this->order);
                CartItem::query()->where('user_id', $this->order->user_id)->delete();
            }

            $this->order->forceFill([
                'status' => $this->order->status === 'pending' ? 'processing' : $this->order->status,
                'payment_status' => 'paid',
            ])->save();
        });

        $this->sendConfirmationEmail();
    }

    private function reduceStockAndRecordSales(): void
    {
        foreach ($this->order->items as $item) {
            if ($item->product_variant_id) {
                ProductVariant::query()
                    ->whereKey($item->product_variant_id)
                    ->where('stock', '>=', $item->quantity)
                    ->decrement('stock', $item->quantity);
            } else {
                Product::query()
                    ->whereKey($item->product_id)
                    ->where('stock', '>=', $item->quantity)
                    ->decrement('stock', $item->quantity);
            }

            Product::query()
                ->whereKey($item->product_id)
                ->increment('total_sales', $item->quantity);
        }
    }

    private function sendConfirmationEmail(): void
    {
        $email = $this->order->user?->email ?? $this->order->guest_email;

        if (! $email) {
            return;
        }

        SendEmailJob::dispatch(
            $email,
            'Order confirmation: '.$this->order->order_number,
            "Thank you for your order {$this->order->order_number}. We received your payment and are preparing your items.",
        );
    }
}
