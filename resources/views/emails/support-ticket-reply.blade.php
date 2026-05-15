@extends('emails.layout')

@section('content')
    <h1>New support reply</h1>
    <p>Hi {{ $data['recipientName'] }},</p>
    <p>{{ $data['senderName'] }} replied to your support ticket.</p>

    <div class="panel">
        <p><strong>Ticket:</strong> {{ $data['ticketNumber'] }}</p>
        <p><strong>Subject:</strong> {{ $data['subject'] }}</p>
        <p>{!! nl2br(e($data['replyMessage'])) !!}</p>
    </div>

    <a class="button" href="{{ $data['actionUrl'] }}">Open conversation</a>
@endsection
