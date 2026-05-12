@extends('emails.layout')

@section('content')
    <h1>Your affiliate tier was upgraded</h1>
    <p>You moved from {{ $data['oldTier'] }} to {{ $data['newTier'] }}.</p>

    <div class="panel">
        <p class="muted">New benefits</p>
        <ul>
            @foreach ($data['benefits'] as $benefit)
                <li>{{ $benefit }}</li>
            @endforeach
        </ul>
    </div>

    <a class="button" href="{{ $data['actionUrl'] }}">Open Affiliate Dashboard</a>
@endsection
