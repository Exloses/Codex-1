@extends('emails.layout')

@section('content')
    <h1>Congratulations, your vendor store is approved</h1>
    <p>{{ $data['storeName'] }} is now active on GlobalDropship. You can manage products, orders, and payouts from your vendor dashboard.</p>

    <a class="button" href="{{ $data['actionUrl'] }}">Go to Vendor Dashboard</a>
@endsection
