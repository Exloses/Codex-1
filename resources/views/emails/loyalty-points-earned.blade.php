@extends('emails.layout')

@section('content')
    <h1>You earned loyalty points</h1>
    <p>Your latest activity added points to your GlobalDropship rewards balance.</p>

    <div class="panel">
        <p class="muted">Points earned</p>
        <p class="metric">{{ $data['pointsEarned'] }}</p>
        <p>Total point balance: {{ $data['totalPoints'] }}</p>
    </div>

    <a class="button" href="{{ $data['actionUrl'] }}">View Loyalty Points</a>
@endsection
