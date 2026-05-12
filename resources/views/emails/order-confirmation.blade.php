@extends('emails.layout')

@section('content')
    <h1>Order {{ $data['orderNumber'] }} is confirmed</h1>
    <p>Thanks for shopping with GlobalDropship. We have received your order and will keep you updated as it moves through fulfillment.</p>

    <h2>Items</h2>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Qty</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data['items'] as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td class="right">{{ $item['quantity'] }}</td>
                    <td class="right">{{ $item['subtotal'] }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Your item list will be available soon.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="panel">
        <p class="muted">Order total</p>
        <p class="metric">{{ $data['total'] }}</p>
    </div>

    <h2>Shipping address</h2>
    <p class="address">{{ $data['shippingAddress'] }}</p>

    <a class="button" href="{{ $data['actionUrl'] }}">Track Order</a>
@endsection
