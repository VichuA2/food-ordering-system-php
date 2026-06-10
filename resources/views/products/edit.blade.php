@extends('layouts.admin')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        
        <div class="card card-info card-outline shadow-sm border-top-info">
            <div class="card-header bg-light">
                <h3 class="card-title m-0"><i class="fas fa-edit mr-2"></i> Edit Food Item</h3>
            </div>
            
            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="name">Food Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $product->name) }}" required autofocus>
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="price">Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" id="price" 
                                   class="form-control @error('price') is-invalid @enderror" 
                                   value="{{ old('price', $product->price) }}" required>
                            @error('price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="category_id">Food Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ (old('category_id') ?? $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label>Select Hotel</label>
                            <select name="hotel_id" class="form-control" required>
                                <option value="">-- Select Hotel --</option>
                                @foreach($hotels as $hotel)
                                <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 form-group mb-3">
                            <label for="image">Update Food Image</label>
                            <div class="custom-file">
                                <input type="file" name="image" id="image" class="custom-file-input @error('image') is-invalid @enderror" accept="image/*">
                                <label class="custom-file-label" for="image">Choose new file...</label>
                            </div>
                            <small class="form-text text-muted">Leave blank if you don't want to change the image.</small>
                            @error('image') <span class="text-danger small">{{ $message }}</span> @enderror
                            
                            @if($product->image)
                                <div class="mt-3">
                                    <p class="mb-1 text-muted small">Current Image:</p>
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="height: 80px; object-fit: cover;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-top">
                    <button type="submit" class="btn btn-info text-white">
                        <i class="fas fa-sync-alt mr-1"></i> Update Product
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary float-right">
                        Cancel
                    </a>
                </div>
            </form>
            
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endpush
@endsection