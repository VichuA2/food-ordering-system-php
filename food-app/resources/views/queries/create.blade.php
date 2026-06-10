@extends('layouts.customer')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm border-0 col-md-8 mx-auto">
        <div class="card-header bg-danger text-white"><h5>Contact Support</h5></div>
        <div class="card-body">
            <form action="{{ route('queries.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Subject</label>
                    <input type="text" name="subject" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Related Order (Optional)</label>
                    <select name="order_id" class="form-control">
                        <option value="">-- Select an Order --</option>
                        @foreach($orders as $order)
                        <option value="{{ $order->id }}">#ORD-{{ $order->id }} - {{ $order->hotel->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Message</label>
                    <textarea name="message" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-danger">Submit Query</button>
            </form>
        </div>
    </div>
</div>
@endsection