@extends('layouts.admin')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-6">
        <div class="card card-primary card-outline shadow-sm border-top-primary">
            <div class="card-header bg-light">
                <h3 class="card-title m-0"><i class="fas fa-plus-circle mr-2"></i>Add New Category</h3>
            </div>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="name">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Enter category name" autofocus>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer bg-white border-top">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Save</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary float-right">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection