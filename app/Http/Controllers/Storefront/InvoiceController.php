<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    public function download(Order $order): Response
    {
        $this->authorize('view', $order);

        $invoiceOrder = Order::query()
            ->select([
                'id',
                'user_id',
                'address_id',
                'order_number',
                'guest_email',
                'guest_name',
                'guest_phone',
                'guest_address_line1',
                'guest_address_line2',
                'guest_city',
                'guest_state',
                'guest_postal_code',
                'guest_country',
                'status',
                'subtotal_usd',
                'shipping_cost_usd',
                'discount_usd',
                'total_usd',
                'buyer_currency',
                'payment_status',
                'payment_method',
                'created_at',
            ])
            ->with([
                'user:id,name,email',
                'address:id,full_name,phone,address_line1,address_line2,city,state,postal_code,country',
                'items:id,order_id,product_id,product_variant_id,quantity,price_usd,subtotal_usd',
                'items.product:id,name,slug',
                'items.productVariant:id,product_id,combination',
            ])
            ->findOrFail($order->id);

        $pdf = Pdf::loadView('invoices.order', [
            'order' => $invoiceOrder,
            'appName' => config('app.name', 'GlobalDropship'),
            'supportEmail' => config('mail.from.address') ?: 'support@globaldropship.test',
        ])->setPaper('a4');

        return $pdf->download("invoice-{$invoiceOrder->order_number}.pdf");
    }
}
