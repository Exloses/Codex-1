<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function download(Order $order): StreamedResponse
    {
        $this->authorize('view', $order);

        $order->load(['items.product:id,name,slug,selling_price', 'address']);

        return response()->streamDownload(function () use ($order) {
            echo "INVOICE\n";
            echo "Order: {$order->order_number}\n";
            echo "Date: {$order->created_at?->format('Y-m-d H:i')}\n";
            echo "Status: {$order->status}\n\n";

            foreach ($order->items as $item) {
                echo "{$item->product?->name} x {$item->quantity} - USD {$item->subtotal_usd}\n";
            }

            echo "\nSubtotal: USD {$order->subtotal_usd}\n";
            echo "Shipping: USD {$order->shipping_cost_usd}\n";
            echo "Discount: USD {$order->discount_usd}\n";
            echo "Total: USD {$order->total_usd}\n";
        }, "invoice-{$order->order_number}.txt", [
            'Content-Type' => 'text/plain',
        ]);
    }
}
