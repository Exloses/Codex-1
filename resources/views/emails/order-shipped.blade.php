@extends('emails.layout')

@section('content')
    <h1>Your shipment is on the way</h1>
    <p>Good news: {{ $data['orderNumber'] }} has shipped.</p>

    <div class="panel">
        <p class="muted">Tracking number</p>
        <p class="metric">{{ $data['trackingNumber'] }}</p>
        <p>Carrier: {{ $data['carrier'] }}</p>
        <p>Estimated arrival: {{ $data['estimatedArrival'] }}</p>
    </div>

    <a class="button" href="{{ $data['actionUrl'] }}">Track Shipment</a>
@endsection
