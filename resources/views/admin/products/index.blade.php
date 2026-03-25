@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    @php($currencySymbol = config('store.currency_symbol', '$'))

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Products</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product</a>
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
                                        <img src="{{ $product->main_image_url }}"
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
                                    {{ $currencySymbol }}{{ number_format($product->sale_price ?? $product->base_price, 2) }}
                                </td>

                                <td>
                                    <span class="badge bg-dark">Qty: {{ $product->total_stock }}</span>

                                    @if($product->has_low_stock)
                                        <span class="badge bg-warning text-dark">Low Stock</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td class="text-end">
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