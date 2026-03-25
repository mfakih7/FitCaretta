@extends('layouts.frontend')

@section('title', 'Order Success - ' . config('store.name'))

@section('content')
    <div class="alert alert-success border-0 shadow-sm">
        <div class="fw-semibold">Order placed successfully.</div>
        <div class="small">Keep your order number for WhatsApp follow-up and tracking.</div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h1 class="h3 mb-2">Thank you! Your order has been placed.</h1>
            <p class="text-muted">Order Number: <strong>{{ $order->order_number }}</strong></p>
            <p class="text-muted mb-0">A confirmation email was attempted for <strong>{{ $order->email }}</strong>.</p>
            <p class="small text-muted">If email is delayed, you can still use your WhatsApp confirmation below.</p>

            <div class="row g-4 mt-2">
                <div class="col-lg-7">
                    <h5>Items</h5>
                    @foreach($order->items as $item)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <div>{{ $item->product_name }}</div>
                                <small class="text-muted">{{ $item->size_name ?? '-' }} / {{ $item->color_name ?? '-' }} x{{ $item->quantity }}</small>
                            </div>
                            <div>{{ config('store.currency_symbol') }}{{ number_format((float) $item->line_total, 2) }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="col-lg-5">
                    <h5>Customer Info</h5>
                    <div class="small text-muted">{{ $order->full_name }}</div>
                    <div class="small text-muted">{{ $order->phone }}</div>
                    <div class="small text-muted">{{ $order->city }}</div>
                    <div class="small text-muted mb-2">{{ $order->address }}</div>

                    <div class="d-flex justify-content-between"><span>Subtotal</span><strong>{{ config('store.currency_symbol') }}{{ number_format((float) $order->subtotal, 2) }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Discount</span><strong class="text-success">-{{ config('store.currency_symbol') }}{{ number_format((float) $order->discount_total, 2) }}</strong></div>
                    <hr>
                    <div class="d-flex justify-content-between"><span class="fw-semibold">Total</span><strong class="fs-5">{{ config('store.currency_symbol') }}{{ number_format((float) $order->total, 2) }}</strong></div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-success fw-semibold">Continue on WhatsApp</a>
                <a href="{{ route('orders.show', $order->public_token) }}" class="btn btn-outline-primary">View Order Details</a>
            </div>
        </div>
    </div>
@endsection
