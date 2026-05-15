@extends('emails.layout')

@section('content')
    <h1>Support ticket opened</h1>
    <p>Hi {{ $data['recipientName'] }},</p>
    <p>We received your support request and the team will follow up from your account support center.</p>

    <div class="panel">
        <p><strong>Ticket:</strong> {{ $data['ticketNumber'] }}</p>
        <p><strong>Subject:</strong> {{ $data['subject'] }}</p>
        <p><strong>Status:</strong> {{ $data['status'] }}</p>
        <p><strong>Priority:</strong> {{ $data['priority'] }}</p>
        <p>{!! nl2br(e($data['message'])) !!}</p>
    </div>

    <a class="button" href="{{ $data['actionUrl'] }}">View ticket</a>
@endsection
