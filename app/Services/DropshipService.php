<?php

namespace App\Services;

use App\Models\DropshipOrder;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DropshipService
{
    public function createDropshipOrders(Order $order): Collection
    {
        $order->loadMissing('items.product.vendor.user');

        return DB::transaction(function () use ($order) {
            $created = new Collection;

            $order->items
                ->groupBy('vendor_id')
                ->each(function ($items, int $vendorId) use ($order, $created) {
                    $dropshipOrder = DropshipOrder::query()->firstOrCreate(
                        ['order_id' => $order->id, 'vendor_id' => $vendorId],
                        [
                            'dropship_number' => $this->generateDropshipNumber(),
                            'status' => 'pending',
                            'vendor_total_idr' => $this->calculateVendorTotalIdr($items),
                            'is_paid_to_vendor' => false,
                            'notes' => 'Generated automatically after payment.',
                        ]
                    );

                    $created->push($dropshipOrder);
                    $this->notifyVendor($dropshipOrder);
                });

            return $created;
        });
    }

    private function calculateVendorTotalIdr(Collection $items): float
    {
        return (float) $items->sum(function ($item) {
            $product = $item->product;
            $variant = $item->productVariant;
            $vendorPrice = $variant?->vendor_price ?? $product?->vendor_price ?? $item->price_usd;

            return (float) $vendorPrice * (int) $item->quantity;
        });
    }

    private function generateDropshipNumber(): string
    {
        do {
            $number = 'DS-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
        } while (DropshipOrder::query()->where('dropship_number', $number)->exists());

        return $number;
    }

    private function notifyVendor(DropshipOrder $dropshipOrder): void
    {
        $dropshipOrder->loadMissing('vendor.user', 'order');
        $vendorEmail = $dropshipOrder->vendor?->user?->email;

        if ($vendorEmail) {
            Mail::raw(
                "New dropship order {$dropshipOrder->dropship_number} is ready for fulfillment.",
                fn ($message) => $message
                    ->to($vendorEmail)
                    ->subject('New dropship order: '.$dropshipOrder->dropship_number)
            );
        }

        Log::info('Dropship order ready for vendor notification.', [
            'dropship_order_id' => $dropshipOrder->id,
            'vendor_id' => $dropshipOrder->vendor_id,
        ]);
    }
}
