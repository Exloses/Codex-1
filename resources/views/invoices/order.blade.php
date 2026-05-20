@php
    $money = fn ($value) => '$'.number_format((float) $value, 2);
    $label = fn ($value) => $value ? ucwords(str_replace('_', ' ', (string) $value)) : 'Not available';
    $customerName = $order->user?->name ?: ($order->guest_name ?: 'Guest customer');
    $customerEmail = $order->user?->email ?: ($order->guest_email ?: 'Not available');
    $shippingName = $order->address?->full_name ?: $customerName;
    $shippingPhone = $order->address?->phone ?: ($order->guest_phone ?: null);
    $shippingLines = array_filter([
        $order->address?->address_line1 ?: $order->guest_address_line1,
        $order->address?->address_line2 ?: $order->guest_address_line2,
        trim(implode(', ', array_filter([
            $order->address?->city ?: $order->guest_city,
            $order->address?->state ?: $order->guest_state,
            $order->address?->postal_code ?: $order->guest_postal_code,
        ]))),
        $order->address?->country ?: $order->guest_country,
    ]);
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #18181b;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        .page {
            padding: 34px;
        }

        .header {
            border-bottom: 2px solid #18181b;
            padding-bottom: 18px;
        }

        .brand {
            color: #0f766e;
            font-size: 22px;
            font-weight: 700;
        }

        .tagline {
            color: #71717a;
            font-size: 11px;
            margin-top: 2px;
        }

        .invoice-title {
            float: right;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-top: -44px;
            text-align: right;
        }

        .meta {
            margin-top: 24px;
            width: 100%;
        }

        .meta td {
            vertical-align: top;
            width: 50%;
        }

        .section-title {
            color: #71717a;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .8px;
            margin: 0 0 6px;
            text-transform: uppercase;
        }

        .box {
            border: 1px solid #d4d4d8;
            border-radius: 4px;
            padding: 12px;
        }

        .muted {
            color: #71717a;
        }

        .items {
            border-collapse: collapse;
            margin-top: 24px;
            width: 100%;
        }

        .items th {
            background: #f4f4f5;
            border-bottom: 1px solid #d4d4d8;
            color: #3f3f46;
            font-size: 10px;
            padding: 9px;
            text-align: left;
            text-transform: uppercase;
        }

        .items td {
            border-bottom: 1px solid #e4e4e7;
            padding: 10px 9px;
            vertical-align: top;
        }

        .right {
            text-align: right;
        }

        .totals {
            margin-left: auto;
            margin-top: 18px;
            width: 260px;
        }

        .totals td {
            padding: 5px 0;
        }

        .grand-total td {
            border-top: 2px solid #18181b;
            font-size: 14px;
            font-weight: 700;
            padding-top: 8px;
        }

        .footer {
            border-top: 1px solid #d4d4d8;
            color: #71717a;
            font-size: 11px;
            margin-top: 34px;
            padding-top: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <main class="page">
        <header class="header">
            <div class="brand">{{ $appName }}</div>
            <div class="tagline">Shop Global, Sourced Local</div>
            <div class="invoice-title">INVOICE</div>
        </header>

        <table class="meta">
            <tr>
                <td style="padding-right: 12px;">
                    <p class="section-title">Invoice Details</p>
                    <div class="box">
                        <strong>Order:</strong> {{ $order->order_number }}<br>
                        <strong>Date:</strong> {{ $order->created_at?->format('M d, Y') ?: 'Not available' }}<br>
                        <strong>Order Status:</strong> {{ $label($order->status) }}<br>
                        <strong>Payment Status:</strong> {{ $label($order->payment_status) }}<br>
                        <strong>Payment Method:</strong> {{ $label($order->payment_method) }}<br>
                        <strong>Currency:</strong> {{ $order->buyer_currency ?: 'USD' }}
                    </div>
                </td>
                <td style="padding-left: 12px;">
                    <p class="section-title">Customer</p>
                    <div class="box">
                        <strong>{{ $customerName }}</strong><br>
                        <span class="muted">{{ $customerEmail }}</span>
                    </div>
                </td>
            </tr>
        </table>

        <table class="meta">
            <tr>
                <td>
                    <p class="section-title">Shipping Address</p>
                    <div class="box">
                        <strong>{{ $shippingName }}</strong><br>
                        @if ($shippingPhone)
                            {{ $shippingPhone }}<br>
                        @endif

                        @forelse ($shippingLines as $line)
                            {{ $line }}<br>
                        @empty
                            <span class="muted">No shipping address is available for this order.</span>
                        @endforelse
                    </div>
                </td>
                <td></td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="right" style="width: 70px;">Qty</th>
                    <th class="right" style="width: 100px;">Unit Price</th>
                    <th class="right" style="width: 100px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($order->items as $item)
                    @php
                        $variant = $item->productVariant?->combination;
                        $variantText = is_array($variant)
                            ? collect($variant)->map(fn ($value, $key) => "{$key}: {$value}")->implode(', ')
                            : null;
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $item->product?->name ?: 'Unavailable product' }}</strong>
                            @if ($variantText)
                                <br><span class="muted">{{ $variantText }}</span>
                            @endif
                        </td>
                        <td class="right">{{ $item->quantity }}</td>
                        <td class="right">{{ $money($item->price_usd) }}</td>
                        <td class="right">{{ $money($item->subtotal_usd) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">No items are available for this order.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td>Subtotal</td>
                <td class="right">{{ $money($order->subtotal_usd) }}</td>
            </tr>
            <tr>
                <td>Shipping</td>
                <td class="right">{{ $money($order->shipping_cost_usd) }}</td>
            </tr>
            <tr>
                <td>Discount</td>
                <td class="right">-{{ $money($order->discount_usd) }}</td>
            </tr>
            <tr class="grand-total">
                <td>Total</td>
                <td class="right">{{ $money($order->total_usd) }}</td>
            </tr>
        </table>

        <footer class="footer">
            Thank you for shopping with {{ $appName }}. For invoice or order support, contact {{ $supportEmail }}.
        </footer>
    </main>
</body>
</html>
