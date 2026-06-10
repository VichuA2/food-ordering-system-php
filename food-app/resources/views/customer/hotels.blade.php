@extends('layouts.customer')

@section('content')
<div class="bg-light py-5 rounded mb-5 shadow-sm">
    <div class="container text-center py-5">
        <h1 class="display-4 fw-bold text-dark mb-3">Hungry? You're in the right place!</h1>
        <p class="lead text-muted mb-4">Order delicious food from the best hotels around you.</p>
        <a href="#hotels" class="btn btn-danger btn-lg px-5 rounded-pill shadow-sm">Explore Hotels</a>
    </div>
</div>

<div id="hotels" class="container mt-5">
    <h3 class="fw-bold mb-4 border-bottom pb-2">Popular Hotels</h3>
    
    <div class="row">
        @forelse($hotels as $hotel)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 h-100 transition-hover">
                    <div class="bg-secondary text-white text-center d-flex align-items-center justify-content-center" style="height: 160px; font-size: 3rem;">
                        
                    </div>
                    
                    <div class="card-body text-center mt-2">
                        <h5 class="card-title fw-bold text-dark">{{ $hotel->name }}</h5>
                        <p class="text-muted small mb-3">Explore our delicious and freshly prepared menu!</p>
                        
                        <a href="{{ url('/hotel/' . $hotel->id . '/menu') }}" class="btn btn-outline-danger rounded-pill px-4">
                            View Menu 
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">
                <h4>No hotels available right now. Check back soon!</h4>
            </div>
        @endforelse
    </div>
</div>
@endsection