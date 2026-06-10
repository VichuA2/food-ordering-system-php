@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="m-0 text-dark">User management</h3>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
        </div>
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{session('success')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('users.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-5 mb-2">
                            <input type="text" name="search" class="form-control" placeholder="Search by Name, Email or Phone..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-4 mb-2">
                            <select name="role" class="form-control">
                                <option value="">-- All Roles --</option>
                                @foreach($roles as $role)
                                <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                    {{ $role }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Search & Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($users->isEmpty())
        <div class="alert alert-warning text-center">
            No users found matching your criteria! <a href="{{ route('users.index') }}">Clear Filters</a>
        </div>
        @endif

        <div class="d-flex justify-content-end mt-3">
            {{ $users->links('pagination::bootstrap-4') }}
        </div>
        <div class="card shadow-sm border-top-primary">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>
                                @if($user->hasRole('Admin'))
                                <span class="badge badge-danger">Admin</span>
                                @elseif($user->hasRole('Delivery Person'))
                                <span class="badge badge-success">Delivery Person</span>
                                @elseif($user->hasRole('Hotel Owner'))
                                <span class="badge badge-warning">Hotel Owner</span>
                                @elseif($user->hasRole('Employee'))
                                <span class="badge badge-info">Employee</span>
                                @else
                                <span class="badge badge-secondary">Customers</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i> Show
                                </a>
                                @if(!(Auth::user()->hasRole('employee') && $user->hasRole('admin')))
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endif

                                @hasrole('Admin')
                                @if($user->id != Auth::id())
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                                @endif
                                @endhasrole
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No users found. Click "Add New User" to create one.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($users, 'links'))
            <div class="card-footer clearfix bg-white">
                {{ $users->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection