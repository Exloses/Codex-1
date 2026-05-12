@extends('emails.layout')

@section('content')
    <h1>New dropship order</h1>
    <p>A new vendor fulfillment order is ready: {{ $data['dropshipNumber'] }}.</p>

    <h2>Items to prepare</h2>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Qty</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data['items'] as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td class="right">{{ $item['quantity'] }}</td>
                </tr>
            @empty
                <tr><td colspan="2">Item details will appear in your vendor dashboard.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Ship to</h2>
    <p class="address">{{ $data['shippingAddress'] }}</p>

    <div class="panel">
        <p class="muted">Fulfillment deadline</p>
        <p class="metric">{{ $data['deadline'] }}</p>
    </div>

    <a class="button" href="{{ $data['actionUrl'] }}">Open Vendor Order</a>
@endsection
