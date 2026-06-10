@extends('layouts.customer')

@section('content')
<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow border-0 py-5">
                <div class="card-body">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    
                    <h2 class="fw-bold text-dark mb-3">Order Placed Successfully!</h2>
                    <p class="text-muted mb-4">
                        Thank you for your order. We have received your request and our partner hotel will start preparing your delicious food right away!
                    </p>
                    
                    @if(session('order_id'))
                    <div class="alert alert-success d-inline-block px-4 py-2 mb-4">
                        <h5 class="mb-0">Order ID: <strong>#ORD-{{ session('order_id') }}</strong></h5>
                    </div>
                    @endif

                    <p class="text-muted small mb-4">
                        You will receive an email confirmation shortly with your order details.
                    </p>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ url('/') }}" class="btn btn-outline-danger rounded-pill px-4">
                            <i class="fas fa-arrow-left"></i> Back to Home
                        </a>
                        <a href="{{route('dashboard')}}" class="btn btn-danger rounded-pill px-4">
                            Track Order <i class="fas fa-motorcycle ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection