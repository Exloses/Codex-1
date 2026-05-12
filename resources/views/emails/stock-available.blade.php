@extends('emails.layout')

@section('content')
    <h1>{{ $data['productName'] }} is back in stock</h1>
    <p>The product you asked about is available again. Popular products can move quickly, so check it while stock is fresh.</p>

    <a class="button" href="{{ $data['actionUrl'] }}">View Product</a>
@endsection
