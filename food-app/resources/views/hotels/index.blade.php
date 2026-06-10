@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="m-0 text-dark"><i class="fas fa-building mr-2"></i>
                Hotel Management
            </h3>
            <a href="{{ route('hotels.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Hotel</a>
        </div>
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{session('success')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        <form action="{{ route('hotels.index') }}" method="GET" class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label for="search" class="form-label mb-1">Search Hotel</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Search by hotel name" value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label for="owner_id" class="form-label mb-1">Filter by Owner</label>
                    <select id="owner_id" name="owner_id" class="form-control">
                        <option value="">All Owners</option>
                        @foreach ($owners as $owner)
                        <option value="{{ $owner->id }}" {{ request('owner_id') == $owner->id ? 'selected' : '' }}>
                            {{ $owner->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </form>
        <div class="card shadow-sm border-top-primary">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Hotel Name</th>
                            <th>Assigned Owner</th>
                            <th>Registered On</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hotels as $hotel)
                        <tr>
                            <td>{{$hotel->id}}</td>
                            <td><strong>{{$hotel->name}}</strong></td>
                            <td>
                                <span class="badge badge-info"><i class="fas fa-user-tie mr-1"></i> {{ $hotel->users->name ?? 'Unassigned' }}</i></span>
                            </td>
                            <td>
                                {{$hotel->created_at->format('d M Y')}}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('hotels.show', $hotel->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i> Show
                                </a>
                                <a href="{{ route('hotels.edit', $hotel->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                <form action="{{ route('hotels.destroy', $hotel->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this hotel?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No Hotels Found. Click "Add New Hotel" to create one.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection