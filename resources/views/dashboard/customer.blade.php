@extends('layouts.customer')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark"> My Profile & Dashboard</h2>
            <p class="text-muted">Welcome back, {{ Auth::user()->name }}! Manage your profile and view your orders here.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-4 bg-light">
                <div class="card-body p-4 text-center">
                    <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 30px;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold">{{ Auth::user()->name }}</h5>
                    <p class="text-muted mb-1"><i class="fas fa-envelope me-2"></i>{{ Auth::user()->email }}</p>
                    <p class="text-muted mb-0"><i class="fas fa-phone me-2"></i>{{ Auth::user()->phone ?? 'No phone added' }}</p>

                    <hr class="my-4">
                    <a href="{{ route('queries.index') }}" class="btn btn-sm btn-outline-dark w-100 rounded-pill mb-2">
                        View My Support Queries
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom pb-0 pt-4 px-4">
                    <h4 class="fw-bold">My Order History</h4>
                </div>
                <div class="card-body p-4">

                    @if($orders->isEmpty())
                    <div class="text-center py-5">
                        <h5 class="text-muted">You haven't placed any orders yet.</h5>
                        <a href="{{ url('/') }}" class="btn btn-danger mt-3 rounded-pill px-4">Order Food Now</a>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td><strong class="text-primary">#ORD-{{ $order->id }}</strong></td>
                                    <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                    <td class="fw-bold text-success">₹{{ number_format($order->total_amount ?? 0, 2) }}</td>
                                    <td>
                                        @if($order->status == 'Pending')
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending</span>
                                        @elseif($order->status == 'Preparing')
                                        <span class="badge bg-info text-dark px-3 py-2 rounded-pill">Preparing</span>
                                        @elseif($order->status == 'Ready for Delivery' || $order->status == 'Out for Delivery')
                                        <span class="badge bg-primary px-3 py-2 rounded-pill">On the way </span>
                                        @elseif($order->status == 'Delivered')
                                        <span class="badge bg-success px-3 py-2 rounded-pill">Delivered </span>
                                        @else
                                        <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ $order->status }}</span>
                                        @endif
                                    </td>
                                    <td colspan="5">
                                        <div class="d-flex justify-content-end">
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">View Details</a>
                                        </div>
                                    </td>
                                </tr>
                            
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection