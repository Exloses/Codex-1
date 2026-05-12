@extends('emails.layout')

@section('content')
    <h1>Your payout has been paid</h1>
    <p>Your approved affiliate payout has been sent.</p>

    <div class="panel">
        <p class="muted">Paid amount</p>
        <p class="metric">{{ $data['amount'] }}</p>
        <p>Transaction reference: {{ $data['transactionRef'] }}</p>
        <p>Paid date: {{ $data['paidDate'] }}</p>
    </div>

    <a class="button" href="{{ $data['actionUrl'] }}">View Payouts</a>
@endsection
