@extends('layouts.admin')

@section('content')
<div class="row mb-3"><div class="col-12"><h2 class="m-0 text-dark">Welcome, {{Auth::user()->name}}</h2></div></div>
<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner"><h3>{{ $analytics['total_orders'] }}</h3><p>Total Orders Received</p></div>
            <a href="{{ route('orders.index') }}" class="small-box-footer">View Orders <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner"><h3>₹{{ number_format($analytics['total_revenue'], 2) }}</h3><p>Total Earnings</p></div>
            
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-primary">
            <div class="inner"><h3>{{ $analytics['picked_orders'] }}</h3><p>Picked / Delivered Orders</p></div>
            
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        <h4 class="text-muted">My Hotels</h4>
        <div class="d-flex gap-3">
            @foreach(\App\Models\Hotel::where('user_id', Auth::id())->get() as $hotel)
                <div class="card p-3 shadow-sm" style="min-width: 200px;">
                    <h6 class="fw-bold">{{ $hotel->name }}</h6>
                    <small class="text-success"> Products: {{$hotel->products->count()}}</small>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection