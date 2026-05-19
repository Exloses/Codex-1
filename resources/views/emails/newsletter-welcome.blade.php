@extends('emails.layout')

@section('content')
    <h1>{{ $data['headline'] }}</h1>
    <p>Hi {{ $data['name'] }},</p>
    <p>{{ $data['intro'] }}</p>

    <a class="button" href="{{ $data['storeUrl'] }}">Explore GlobalDrop</a>
@endsection
