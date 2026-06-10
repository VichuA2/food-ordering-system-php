@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="m-0 text-dark"><i class="fas fa-utensils mr-2"></i>Products</h3>
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Product
            </a>
        </div>

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <div class="card shadow-sm border-top-primary">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th>Image</th>
                            <th>Food Name</th>
                            <th>Hotel Name</th>
                            <th>Category</th>
                            <th>Price (₹)</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                        <tr>
                            <td class="align-middle">
                                @if($product->image)
                                <img src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;"> @else
                                <span class="badge badge-secondary">No Image</span>
                                @endif
                            </td>
                            <td class="align-middle"><strong>{{ $product->name }}</strong></td>
                            <td class="align-middle"><strong>{{ $product->hotel->name}}</strong></td>
                            <td class="align-middle">
                                <span class="badge badge-info">{{ $product->category->name ?? 'Uncategorized' }}</span>
                            </td>
                            <td class="align-middle text-success font-weight-bold">₹{{ number_format($product->price, 2) }}</td>
                            <td class="text-center align-middle">
                                <form action="{{ route('products.toggle', $product->id) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $product->is_available ? 'btn-success' : 'btn-danger' }}"
                                        title="Toggle Availability">
                                        <i class="fas {{ $product->is_available ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    </button>
                                </form>

                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-edit" title="Edit"></i>
                                </a>

                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                        
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-hamburger fa-3x mb-3 text-light"></i><br>
                                No products found in your menu. Click "Add New Product" to create your first food item!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($products, 'links'))
            <div class="card-footer clearfix bg-white">
                {{ $products->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection