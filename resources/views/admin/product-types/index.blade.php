@extends('layouts.admin')

@section('title', 'Product Types')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Product Types</h1>
        <a href="{{ route('admin.product-types.create') }}" class="btn btn-primary">Add Product Type</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($productTypes as $productType)
                    <tr>
                        <td>{{ $productType->id }}</td>
                        <td>{{ $productType->name }}</td>
                        <td>{{ $productType->slug }}</td>
                        <td>
                            <span class="badge {{ $productType->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $productType->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.product-types.edit', $productType) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.product-types.destroy', $productType) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this product type?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No product types found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $productTypes->links() }}
    </div>
@endsection
