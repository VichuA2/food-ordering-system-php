@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">User Information</h3>
    </div>
    <div class="card-body">
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Roles:</strong> 
            @foreach($user->roles as $role)
                <span class="badge badge-info">{{ $role->name }}</span>
            @endforeach
        </p>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
</div>
@endsection