@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Products</h1>
            @if($products->total() > $products->count())
                <div class="text-muted small">Showing {{ $products->count() }} of {{ $products->total() }}</div>
            @else
                <div class="text-muted small">{{ $products->total() }} total</div>
            @endif
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product</a>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.products.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small mb-1">Search by name</label>
                    <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Type a product name...">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @if($products->count())
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $product->main_image_url }}{{ $product->updated_at ? ('?v=' . $product->updated_at->timestamp) : '' }}"
                                             alt="{{ $product->name }}"
                                             style="width: 42px; height: 42px; object-fit: cover;"
                                             class="rounded border">
                                        <div>
                                            <div>{{ $product->name }}</div>
                                            <div class="small text-muted">{{ $product->slug }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->category?->name ?? '-' }}</td>
                                <td>{{ $product->productType?->name ?? '-' }}</td>

                                <td>
                                    {{ config('store.currency_symbol') }}{{ number_format($product->sale_price ?? $product->base_price, 2) }}
                                </td>

                                <td>
                                    <span class="badge bg-dark">Qty: {{ $product->total_stock }}</span>

                                    @if($product->has_low_stock)
                                        <span class="badge bg-warning text-dark">Low Stock</span>
                                    @endif
                                </td>

                                <td>
                                    @if(method_exists($product, 'trashed') && $product->trashed())
                                        <span class="badge bg-danger">Deleted</span>
                                    @else
                                        <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    @if(method_exists($product, 'trashed') && $product->trashed())
                                        <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Restore this product?')">Restore</button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                           class="btn btn-sm btn-outline-primary">Edit</a>

                                        <form action="{{ route('admin.products.destroy', $product) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Delete this product?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                No products found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $products->links() }}
    </div>
@endsection