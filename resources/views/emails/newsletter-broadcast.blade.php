@extends('emails.layout')

@section('content')
    <h1>{{ $data['subject'] }}</h1>
    <p>{!! nl2br(e($data['message'])) !!}</p>

    <a class="button" href="{{ $data['storeUrl'] }}">Shop GlobalDrop</a>
@endsection
