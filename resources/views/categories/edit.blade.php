@extends('layouts.admin')
@section('content')
<div class="row" justify-content-center mt-4>
    <div class="col-md-6">
        <div class="card card-info card-outline shadow-sm border-top-info">
            <div class="card-header bg-light">
                <h3 class="card-title m-0"><i class="fas fa-edit mr-2"></i>Edit Categgory</h3>
            </div>
            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="name">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{old( 'name',$category->name )}}" required autofocus>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer bg-white border-top">
                    <button type="submit" class="btn btn-info text-white"><i class="fas fa-sync-alt mr-1"></i>Update Category</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary float-right">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection