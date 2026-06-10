@extends('layouts.customer')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4">Your Cart</h3>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($cartItems->isEmpty())
        <div class="text-center py-5 bg-white shadow-sm rounded">
            <h4 class="text-muted fw-bold">Your cart is empty!</h4>
            <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
            <a href="{{ url('/') }}" class="btn btn-danger px-4 rounded-pill">Browse Hotels</a>
        </div>
    @else
        <div class="row">
            <div class="col-md-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush rounded">
                            @php $total = 0; @endphp
                            
                            @foreach($cartItems as $item)
                                @php 
                                    $subtotal = $item->product->price * $item->quantity; 
                                    $total += $subtotal; 
                                @endphp
                                
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <div class="d-flex align-items-center">
                                        @if($item->product->image)
                                            <img src="{{ asset('uploads/products/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;" alt="{{ $item->product->name }}">
                                        @else
                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px; font-size: 24px;">🍲</div>
                                        @endif
                                        
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $item->product->name }}</h6>
                                            <small class="text-muted">₹{{ number_format($item->product->price, 2) }} x {{ $item->quantity }}</small>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold me-4 text-dark">₹{{ number_format($subtotal, 2) }}</span>
                                        
                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="m-0" onsubmit="return confirm('Remove this item from cart?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Remove Item">
                                                ❌
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body p-4">
                        <h5 class="fw-bold border-bottom pb-3 mb-3">Order Summary</h5>
                        
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Item Total</span>
                            <span>₹{{ number_format($total, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="fw-bold mb-0">Grand Total</h5>
                            <h5 class="fw-bold text-danger mb-0">₹{{ number_format($total, 2) }}</h5>
                        </div>
                        
                        <a href="{{ route('orders.checkout') }}" class="btn btn-danger w-100 rounded-pill fw-bold py-2">
                            Proceed to Checkout 
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection