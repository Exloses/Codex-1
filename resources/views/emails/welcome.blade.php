@extends('emails.layout')

@section('content')
    <h1>Welcome to GlobalDropship, {{ $data['name'] }}</h1>
    <p>Your account is ready. You can browse curated products from Indonesian vendors, manage orders, and track shipments from your account.</p>

    <a class="button" href="{{ $data['storeUrl'] }}">Visit Store</a>
    <p style="margin-top: 18px;"><a href="{{ $data['accountUrl'] }}">Go to your account</a></p>
@endsection
