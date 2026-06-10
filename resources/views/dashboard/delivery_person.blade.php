@extends('layouts.admin')

@section('content')
<div class="row mb-3"><div class="col-12"><h2 class="m-0 text-dark">Welcome, Delivery Partner!</h2></div></div>
<div class="row">
    <div class="col-lg-6 col-12">
        <div class="small-box bg-warning">
            <div class="inner"><h3>{{ $assignedOrders }}</h3><p>Total Assigned Orders</p></div>
            <a href="{{route('orders.index')}}" class="small-box-footer">View Deliveries <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-6 col-12">
        <div class="small-box bg-success">
            <div class="inner"><h3>{{ $deliveredOrders }}</h3><p>Successfully Delivered</p></div>
        </div>
    </div>
</div>
@endsection