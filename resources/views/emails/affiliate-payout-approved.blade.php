@extends('emails.layout')

@section('content')
    <h1>Your payout was approved</h1>
    <p>The finance team approved your affiliate payout request.</p>

    <div class="panel">
        <p class="muted">Approved amount</p>
        <p class="metric">{{ $data['amount'] }}</p>
        <p>Payout method: {{ $data['payoutMethod'] }}</p>
        <p>Estimated processing time: {{ $data['estimatedProcess'] }}</p>
    </div>

    <a class="button" href="{{ $data['actionUrl'] }}">View Payouts</a>
@endsection
