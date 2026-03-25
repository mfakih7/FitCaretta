@extends('layouts.frontend')

@section('title', 'Checkout - ' . config('store.name'))

@section('content')
    @php($currencySymbol = config('store.currency_symbol', '$'))
    <h1 class="h3 mb-3">Checkout</h1>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Customer Information</h5>
                    <form method="POST" action="{{ route('checkout.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City / Area</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Place Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Order Summary</h5>
                    @foreach($items as $item)
                        <div class="d-flex justify-content-between border-bottom py-2 small">
                            <div class="d-flex gap-2">
                                <img src="{{ (new \App\Models\Catalog\Product)->resolveImageUrl($item['image_path'] ?? null) }}" alt="{{ $item['name'] }}" class="rounded border" style="width:48px;height:48px;object-fit:cover;">
                                <div>
                                    <div>{{ $item['name'] }}</div>
                                    <div class="text-muted">{{ $item['size_name'] ?? '-' }} / {{ $item['color_name'] ?? '-' }} x{{ $item['quantity'] }}</div>
                                </div>
                            </div>
                            <div>{{ $currencySymbol }}{{ number_format($item['item_total'], 2) }}</div>
                        </div>
                    @endforeach

                    <div class="pt-3">
                        <div class="d-flex justify-content-between"><span>Subtotal</span><strong>{{ $currencySymbol }}{{ number_format($summary['subtotal'], 2) }}</strong></div>
                        <div class="d-flex justify-content-between"><span>Discount</span><strong class="text-success">-{{ $currencySymbol }}{{ number_format($summary['discount_total'], 2) }}</strong></div>
                        <hr>
                        <div class="d-flex justify-content-between"><span class="fw-semibold">Total</span><strong class="fs-5">{{ $currencySymbol }}{{ number_format($summary['total'], 2) }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
