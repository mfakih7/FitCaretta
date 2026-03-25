@extends('layouts.frontend')

@section('title', 'Cart - ' . config('store.name'))

@section('content')
    @php($currencySymbol = config('store.currency_symbol', '$'))
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Your Cart</h1>
        @if($items->isNotEmpty())
            <form method="POST" action="{{ route('cart.clear') }}">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Clear all cart items?')">Clear Cart</button>
            </form>
        @endif
    </div>

    @if($items->isEmpty())
        <div class="alert alert-light border">
            Your cart is empty. <a href="{{ route('shop') }}" class="text-decoration-none">Continue shopping</a>.
        </div>
    @else
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Variant</th>
                            <th>Price</th>
                            <th style="width: 170px;">Quantity</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ (new \App\Models\Catalog\Product)->resolveImageUrl($item['image_path'] ?? null) }}" alt="{{ $item['name'] }}" class="rounded border" style="width:60px;height:60px;object-fit:cover;">
                                    <div>
                                        <a href="{{ route('products.show', $item['slug']) }}" class="text-decoration-none">{{ $item['name'] }}</a>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item['size_name'] ?? '-' }} / {{ $item['color_name'] ?? '-' }}</td>
                            <td>
                                @if($item['discounted_price'] < $item['base_price'])
                                    <div class="small text-muted text-decoration-line-through">{{ $currencySymbol }}{{ number_format($item['base_price'], 2) }}</div>
                                @endif
                                <div class="fw-semibold">{{ $currencySymbol }}{{ number_format($item['discounted_price'], 2) }}</div>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('cart.update', $item['key']) }}" class="d-flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" class="form-control form-control-sm" name="quantity" min="1" max="{{ $item['stock_qty'] }}" value="{{ $item['quantity'] }}">
                                    <button class="btn btn-sm btn-outline-primary" type="submit">Update</button>
                                </form>
                                <div class="small text-muted mt-1">Stock: {{ $item['stock_qty'] }}</div>
                            </td>
                            <td class="fw-semibold">{{ $currencySymbol }}{{ number_format($item['item_total'], 2) }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('cart.destroy', $item['key']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this item?')">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 ms-auto">
                        <div class="d-flex justify-content-between mb-1"><span>Subtotal</span><strong>{{ $currencySymbol }}{{ number_format($summary['subtotal'], 2) }}</strong></div>
                        <div class="d-flex justify-content-between mb-1"><span>Discount</span><strong class="text-success">-{{ $currencySymbol }}{{ number_format($summary['discount_total'], 2) }}</strong></div>
                        <hr>
                        <div class="d-flex justify-content-between"><span class="fw-semibold">Total</span><strong class="fs-5">{{ $currencySymbol }}{{ number_format($summary['total'], 2) }}</strong></div>
                        <a href="{{ route('checkout.index') }}" class="btn btn-dark w-100 mt-3 py-2">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
