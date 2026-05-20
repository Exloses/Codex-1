@extends('emails.layout')

@section('content')
    <h1>New product question</h1>
    <p>A customer asked a question about {{ $data['productName'] }}.</p>

    <div class="panel">
        <p class="muted">Question</p>
        <p>{{ $data['questionExcerpt'] }}</p>
    </div>

    <p>Open the product page to review the public question and post a helpful answer.</p>

    <a class="button" href="{{ $data['actionUrl'] }}">View Question</a>
@endsection
