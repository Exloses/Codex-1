@extends('emails.layout')

@section('content')
    <h1>Your affiliate account is ready</h1>
    <p>Welcome to the GlobalDropship affiliate program. Share your referral link and earn commissions on eligible orders.</p>

    <div class="panel">
        <p class="muted">Referral code</p>
        <p class="metric">{{ $data['referralCode'] }}</p>
        <p>Referral link: <a href="{{ $data['referralLink'] }}">{{ $data['referralLink'] }}</a></p>
        <p>Commission rate: {{ $data['commissionRate'] }}</p>
    </div>

    <a class="button" href="{{ $data['actionUrl'] }}">Open Affiliate Dashboard</a>
@endsection
