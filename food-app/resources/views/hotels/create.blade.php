@extends('layouts.admin')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-6">
        
        <div class="card card-primary card-outline shadow-sm border-top-primary">
            <div class="card-header bg-light">
                <h3 class="card-title m-0"><i class="fas fa-building mr-2"></i> Add New Hotel</h3>
            </div>
            
            <form action="{{ route('hotels.store') }}" method="POST">
                @csrf
                
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="name">Hotel Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" placeholder="Enter hotel name (e.g., A2B, KFC)" required autofocus>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="user_id">Assign Hotel Owner <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                            <option value="">-- Select an Owner --</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}" {{ old('user_id') == $owner->id ? 'selected' : '' }}>
                                    {{ $owner->name }} ({{ $owner->email }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Only users with the 'Hotel Owner' role are listed here.</small>
                        @error('user_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="card-footer bg-white border-top">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save Hotel
                    </button>
                    <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary float-right">
                        Cancel
                    </a>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection