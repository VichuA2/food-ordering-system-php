@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h3 class="card-title fw-bold"><i class="fas fa-shopping-cart mr-2"></i> My Orders</h3>
            @hasrole('Customer')
            <a href="{{ route('customer.hotels') }}" class="btn btn-outline-danger btn-sm">Order More Food</a>
            @endhasrole
        </div>

        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success m-3">{{ session('success') }}</div>
            @endif
            @hasrole('Customer')
            @if($orders->isEmpty())
                <div class="text-center py-5 text-muted"><h4>You haven't ordered anything yet!</h4></div>
            @endhasrole
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="bg-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Hotel Name</th>
                                <th>Location</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td class="fw-bold">#00{{ $order->id }}</td>
                                    <td>{{ $order->hotel->name ?? 'N/A' }}</td>
                                    <td>{{ $order->delivery_location }}</td>
                                    <td class="text-success fw-bold">₹{{ $order->total_amount }}</td>
                                    <td>
                                        <span class="badge {{ $order->status == 'Delivered' ? 'bg-success' : 'bg-warning' }} px-3 py-2">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                            @role('Delivery Person')
                                                @if(is_null($order->delivery_person_id))
                                                    <form action="{{ route('orders.pick', $order->id) }}" method="POST" class="m-0">
                                                        @csrf <button class="btn btn-sm btn-warning">Pick</button>
                                                    </form>
                                                @elseif($order->delivery_person_id == Auth::id() && $order->status == 'Picked')
                                                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="m-0">
                                                        @csrf @method('PUT')
                                                        <input type="hidden" name="status" value="Delivered">
                                                        <button class="btn btn-sm btn-success">Done</button>
                                                    </form>
                                                @endif
                                            @endrole

                                            @role('Hotel Owner')
                                                @if($order->status == 'Pending')
                                                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="m-0">
                                                        @csrf @method('PUT')
                                                        <input type="hidden" name="status" value="Preparing Food">
                                                        <button class="btn btn-sm btn-info">Start</button>
                                                    </form>
                                                @endif
                                            @endrole

                                        </div>
                                    </td>
                                </tr>
                                 @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-hamburger fa-3x mb-3 text-light"></i><br>
                                No Orders found!
                            </td>
                        </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection