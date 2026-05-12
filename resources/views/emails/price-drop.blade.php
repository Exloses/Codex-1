@extends('emails.layout')

@section('content')
    <h1>Price drop on {{ $data['productName'] }}</h1>
    <p>A product on your alert list has a new lower price.</p>

    <div class="panel">
        <p>Old price: {{ $data['oldPrice'] }}</p>
        <p class="metric">New price: {{ $data['newPrice'] }}</p>
    </div>

    <a class="button" href="{{ $data['actionUrl'] }}">View Product</a>
@endsection
