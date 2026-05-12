@extends('emails.layout')

@section('content')
    <h1>You earned a commission</h1>
    <p>An affiliate order triggered a new commission for your account.</p>

    <div class="panel">
        <p class="muted">Commission amount</p>
        <p class="metric">{{ $data['commissionAmount'] }}</p>
        <p>Triggered by order: {{ $data['orderNumber'] }}</p>
        <p>Total balance: {{ $data['totalBalance'] }}</p>
    </div>

    <a class="button" href="{{ $data['actionUrl'] }}">View Commissions</a>
@endsection
