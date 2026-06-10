@extends(Auth::user()->hasRole('Customer') ? 'layouts.customer' : 'layouts.admin')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom p-4">
            <h4 class="fw-bold">Order Details: #ORD-{{ $order->id }}</h4>
            <span class="badge {{ $order->status == 'Delivered' ? 'bg-success' : 'bg-warning' }}">{{ $order->status }}</span>
        </div>
        
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <h6 class="fw-bold text-muted">Customer Details</h6>
                    <p class="mb-1">{{ $order->customer->name }}</p>
                    <p class="text-muted">{{ $order->customer->phone ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-4">
                    <h6 class="fw-bold text-muted">Hotel & Delivery</h6>
                    <p class="mb-1"><strong>Hotel:</strong> {{ $order->hotel->name }}</p>
                    <p class="text-muted small"><strong>Address:</strong> {{ $order->delivery_location }}</p>
                    @hasanyrole('Admin|Employee|Hotel Owner|Customer')
                    <p class="mb-1 mt-3"><strong>Delivery Person:</strong> {{ $order->deliveryPerson->name ?? 'Not assigned yet' }}</p>
                    <p class="text-muted small"><strong>Contact:</strong> {{ $order->deliveryPerson->email ?? 'Not assigned yet' }}</p>
                    @if($order->deliveryPerson && $order->deliveryPerson->phone)
                        <p class="text-muted small"><strong>Phone:</strong> {{ $order->deliveryPerson->phone }}</p>
                    @endif
                    @endhasanyrole
                </div>
            </div>

            <h6 class="fw-bold text-muted mt-3">Ordered Items</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderitems as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ number_format($item->price, 2) }}</td>
                            <td>₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-end mt-3">
                <h5>Grand Total: <span class="text-success fw-bold">₹{{ number_format($order->total_amount, 2) }}</span></h5>
            </div>
        </div>
    </div>
</div>
@endsection