@extends('layouts.customer')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="fw-bold text-dark"><i class="fas fa-shopping-cart text-danger"></i>Checkout</h2>
            <p class="text-muted">Review your items and enter delivery details.</p>
        </div>
    </div>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom pb-0 pt-3">
                    <h5 class="fw-bold"><i class="fas fa-map-marker-alt text-danger mr-2"></i> Delivery Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.place') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" value="{{ Auth::user()->name }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', Auth::user()->phone) }}" required placeholder="10-digit mobile number">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="delivery_location" class="form-label fw-bold">Full Delivery Address <span class="text-danger">*</span></label>
                            <textarea name="delivery_location" id="delivery_location" rows="3" class="form-control @error('delivery_location') is-invalid @enderror" required placeholder="Enter Door No, Street, Area, and Landmark">{{ old('delivery_location') }}</textarea>
                            @error('delivery_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <h5 class="fw-bold mt-4 fs-5 border-bottom pb-2">Select Payment Method</h5>

                        <div class="mb-4">
                            <select class="form-select fs-6 form-select-lg bg-light" name="payment_method" id="payment_method" required>
                                <option value="Cash On Delivery" selected>Cash on Delivery (COD)</option>
                                <option value="UPI"> UPI (GPay, PhonePe, Paytm)</option>
                                <option value="Card"> Credit / Debit Card</option>
                            </select>
                        </div>
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-danger btn-lg rounded-pill fw-bold">
                                Confirm Order & Pay
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-header bg-light border-bottom pb-0 pt-3">
                    <h5 class="fw-bold"><i class="fas fa-receipt text-secondary mr-2"></i> Order Summary</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-3">
                        @php $total = 0; @endphp
                        @forelse($cartItems as $item)
                        <li class="list-group-item d-flex justify-content-between lh-sm bg-transparent px-0">
                            <div>
                                <h6 class="my-0 fw-semibold">{{ $item->product->name }}</h6>
                                <small class="text-muted">{{ $item->product->hotel->name }} | Qty: {{ $item->quantity }}</small>
                            </div>
                            <span class="text-muted">₹{{ number_format($item->product->price * $item->quantity, 2) }}</span>
                        </li>
                        @php $total += ($item->product->price * $item->quantity); @endphp
                        @empty
                        <li class="list-group-item bg-transparent px-0 text-center text-muted">Your cart is empty.</li>
                        @endforelse
                    </ul>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <h5 class="fw-bold mb-0">Total Amount</h5>
                        <h4 class="fw-bold text-success mb-0">₹{{ number_format($total, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection