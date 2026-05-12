@extends('emails.layout')

@section('content')
    <h1>Return request updated</h1>
    <p>Return {{ $data['returnNumber'] }} has a new status.</p>

    <div class="panel">
        <p class="muted">New status</p>
        <p class="metric">{{ $data['newStatus'] }}</p>
        <p>Admin notes: {{ $data['adminNotes'] }}</p>
    </div>

    <a class="button" href="{{ $data['actionUrl'] }}">View Return Request</a>
@endsection
