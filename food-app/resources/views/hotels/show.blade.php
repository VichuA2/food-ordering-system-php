@extends('layouts.admin')

@section('title', 'Hotel Details')

@section('content')
<div class="card card-success card-outline">
    <div class="card-header">
        <h3 class="card-title">Hotel: {{ $hotel->name }}</h3>
    </div>
    <div class="card-body">
        <p><strong>Owner:</strong> {{ $hotel->users->name ?? 'N/A' }}</p>
        
        <h5 class="mt-4">Menu Items:</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hotel->products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? 'No Category' }}</td>
                    <td>₹{{ $product->price }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('hotels.index') }}" class="btn btn-secondary mt-3">Back to List</a>
    </div>
</div>
@endsection