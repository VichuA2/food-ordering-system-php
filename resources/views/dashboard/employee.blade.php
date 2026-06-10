@extends('layouts.admin')

@section('content')
<div class="row mb-3"><div class="col-12"><h2 class="m-0 text-dark">Welcome, {{Auth::user()->name}}</h2><p class="text-muted">Here is your management overview.</p></div></div>
<div class="row">
    
    <div class="col-lg-4 col-6">
        <div class="small-box bg-warning">
            <div class="inner"><h3>{{ $totalUsers }}</h3><p>Total Registered Users</p></div>
            
            <a href="{{ route('users.index') }}" class="small-box-footer">Manage Users <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner"><h3>{{ $totalHotels }}</h3><p>Registered Hotels</p></div>
            <a href="{{ route('hotels.index') }}" class="small-box-footer">Manage Hotels <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-4 col-12">
        <div class="small-box bg-danger">
            <div class="inner"><h3>{{ $totalQueries }}</h3><p>Customer Support Queries</p></div>
            <a href="{{ route('queries.index') }}" class="small-box-footer">View Queries <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

</div>
@endsection