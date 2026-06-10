@extends('layouts.admin')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <h3 class="m-0 text-dark">Admin Dashboard</h3>
        <p>Welcome back, <strong>{{Auth::user()->name}}</strong>!</p>
    </div>
</div>

<div class="row">
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box border shadow-sm">
            <span class="info-box-icon bg-primary text-white"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Total Registered Users</span>
                <span class="info-box-number h4 mb-0">{{$totalUsers}}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box border shadow-sm">
            <span class="info-box-icon bg-primary text-white"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Total Hotel Owners</span>
                <span class="info-box-number h4 mb-0">{{$totalHotelOwners}}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box border shadow-sm">
            <span class="info-box-icon bg-success text-white"><i class="fas fa-building"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Total Partners Hotels</span>
                <span class="info-box-number h4 mb-0">{{$totalHotels}}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box border shadow-sm">
            <span class="info-box-icon bg-warning text-white"><i class="fas fa-shopping-cart"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Total Orders Placed</span>
                <span class="info-box-number h4 mb-0">{{$totalOrders}}</span>
            </div>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-12">
        <div class="card border shadow-sm">
            <div class="card-header bg-light">
                <h3 class="card-title"><i class="fas fa-bolt mr-2"></i>Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('users.index') }}" class="btn btn-outline-primary mr-2">Manage Users</a>
                <a href="{{ route('hotels.index') }}" class="btn btn-outline-success mr-2">Manage Hotels</a>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-info">Manage Categories</a>
            </div>
        </div>
    </div>
</div>
@endsection