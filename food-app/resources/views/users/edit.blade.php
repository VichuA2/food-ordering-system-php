@extends('layouts.admin')
@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card card-info card-outline shadow-sm border-top-info">
            <div class="card-header bg-light">
                <h3 class="card-title m-0"><i class="fas fa-user-edit mr-2"></i>Edit User Information</h3>
            </div>

            <form action="{{route('users.update',$user->id)}}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name', $user->name)}}" required autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{old('email', $user->email)}}" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="phone">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{old('phone', $user->phone)}}" required>
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="role">Assign Role <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                                <option value="">-- Select a Role --</option>
                                @foreach($roles as $role)
                                <option value="{{ $role }}" {{old('role', $user->role) == $role ? 'selected' : ''}}>
                                    {{ $role }}
                                </option>
                                @endforeach
                            </select>
                            @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top">
                    <button type="submit" class="btn btn-info text-white"><i class="fas fa-sync-alt mr-1"></i>Update User</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary float-right">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection