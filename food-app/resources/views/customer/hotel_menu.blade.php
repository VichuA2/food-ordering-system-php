@extends('layouts.customer') @section('content')
<div class="container mt-4">

    <div class="row mb-4 align-items-center border-bottom pb-3">
        <div class="col">
            <h2 class="display-6 fw-bold text-dark">{{ $hotel->name }} - Menu</h2>
        </div>
        <div class="col-auto">
            <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                &larr; Back to Hotels
            </a>
            @auth
            <a href="{{ route('cart.index') }}" class="btn btn-success ms-2">
                🛒 View Cart
            </a>
            @endauth
        </div>
    </div>
    <div id="toast-container"></div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        @forelse($products as $product)
        <div class="col">
            <div class="card h-100 shadow-sm border-0 transition-hover">

                @if($product->image)
                <img src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->name }}" class="card-img-top rounded-top" style="height: 180px; object-fit: cover;">
                @else
                <div class="bg-light d-flex align-items-center justify-content-center rounded-top" style="height: 180px;">
                    <span class="text-secondary">No Image Available</span>
                </div>
                @endif

                <div class="card-body d-flex flex-column">
                    <span class="badge bg-secondary mb-2 align-self-start">{{ $product->category->name ?? 'Special' }}</span>
                    <h5 class="card-title fw-bold">{{ $product->name }}</h5>

                    <div class="small text-muted mb-2">
                        Price: ₹{{ number_format($product->price, 2) }}
                    </div>

                    <div class="mt-auto d-flex flex-column gap-2">
                        @auth
                        <form action="{{ route('cart.add') }}" method="POST" class="product-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="mb-2">
                                <label class="form-label small mb-1">Quantity</label>
                                <input
                                    type="number"
                                    name="quantity"
                                    value="1"
                                    min="1"
                                    class="form-control form-control-sm qty-input"
                                    data-price="{{ $product->price }}"
                                    data-product-id="{{ $product->id }}">
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small text-muted">Live total</span>
                                <strong class="text-success">
                                    <span id="product-total-{{ $product->id }}" class="live-total">₹{{ number_format($product->price, 2) }}</span>
                                </strong>
                            </div>

                            <button type="submit" class="btn btn-outline-danger btn-sm fw-bold w-100">Add to Cart</button>
                        </form>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Login to order</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center mt-5">
            <h4 class="text-muted">Oops! No food items available for this hotel yet. Please check back later.</h4>
        </div>
        @endforelse
    </div>
</div>
<script>
    function updateLiveTotal(input) {
        const productId = input.dataset.productId;
        const price = parseFloat(input.dataset.price);
        const quantity = parseInt(input.value || 1);

        if (isNaN(quantity) || quantity < 1) {
            input.value = 1;
        }

        const safeQuantity = Math.max(parseInt(input.value || 1), 1);
        const total = safeQuantity * price;

        const totalSpan = document.getElementById('product-total-' + productId);
        if (totalSpan) {
            totalSpan.textContent = total.toFixed(2);
        }
    }

    document.querySelectorAll('.qty-input').forEach(function(input) {
        input.addEventListener('input', function() {
            updateLiveTotal(this);
        });
    });

    $(document).ready(function() {
        $('.product-form').on('submit', function(e) {
            e.preventDefault();

            let $form = $(this);
            let actionUrl = $form.attr('action');
            let $submitBtn = $form.find('button[type="submit"]');
            let originalText = $submitBtn.text();

            $submitBtn.text('Adding...').prop('disabled', true);

            $.ajax({
                url: actionUrl,
                type: "POST",
                data: $form.serialize(), 
                dataType: 'json',
                success: function(response) {
                    let toastHtml = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ${response.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
                    $('#toast-container').append(toastHtml);
                    setTimeout(function() {
                        $('.alert').fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }, 3000);
                 
                    $submitBtn.text('Added!')
                        .removeClass('btn-outline-danger')
                        .addClass('btn-success');
                    setTimeout(function() {
                        $submitBtn.text(originalText)
                            .prop('disabled', false)
                            .removeClass('btn-success')
                            .addClass('btn-outline-danger');
                    }, 2000);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    $submitBtn.text('Failed!').prop('disabled', false);
                }
            });
        });
    });
</script>
@endsection