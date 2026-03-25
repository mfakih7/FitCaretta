@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Order {{ $order->order_number }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>Customer:</strong> {{ $order->full_name }}</div>
                <div class="col-md-4"><strong>Email:</strong> {{ $order->email }}</div>
                <div class="col-md-4"><strong>Phone:</strong> {{ $order->phone }}</div>
                <div class="col-md-4"><strong>City:</strong> {{ $order->city }}</div>
                <div class="col-md-8"><strong>Address:</strong> {{ $order->address }}</div>
            </div>

            <hr>

            <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="row g-2 align-items-end">
                @csrf
                @method('PATCH')
                <div class="col-md-4">
                    <label class="form-label">Order Status</label>
                    <select name="order_status" class="form-select">
                        @foreach(['pending', 'confirmed', 'delivered', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected($order->order_status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" type="submit">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Variant</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->size_name ?? '-' }} / {{ $item->color_name ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ config('store.currency_symbol') }}{{ number_format((float) $item->discounted_price, 2) }}</td>
                        <td>{{ config('store.currency_symbol') }}{{ number_format((float) $item->line_total, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="row mt-2">
                <div class="col-md-4 ms-auto">
                    <div class="d-flex justify-content-between"><span>Subtotal</span><strong>{{ config('store.currency_symbol') }}{{ number_format((float) $order->subtotal, 2) }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Discount</span><strong class="text-success">-{{ config('store.currency_symbol') }}{{ number_format((float) $order->discount_total, 2) }}</strong></div>
                    <hr>
                    <div class="d-flex justify-content-between"><span class="fw-semibold">Total</span><strong class="fs-5">{{ config('store.currency_symbol') }}{{ number_format((float) $order->total, 2) }}</strong></div>
                </div>
            </div>
        </div>
    </div>
@endsection
