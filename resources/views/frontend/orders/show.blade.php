@extends('layouts.frontend')

@section('title', 'Order ' . $order->order_number . ' - ' . config('store.name'))

@section('content')
    @php($currencySymbol = config('store.currency_symbol', '$'))
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h1 class="h4 mb-1">Order Details</h1>
            <p class="text-muted mb-3">Order Number: <strong>{{ $order->order_number }}</strong></p>

            @foreach($order->items as $item)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <div>
                        <div>{{ $item->product_name }}</div>
                        <small class="text-muted">{{ $item->size_name ?? '-' }} / {{ $item->color_name ?? '-' }} x{{ $item->quantity }}</small>
                    </div>
                    <div>{{ $currencySymbol }}{{ number_format((float) $item->line_total, 2) }}</div>
                </div>
            @endforeach

            <div class="row mt-4">
                <div class="col-md-5 ms-auto">
                    <div class="d-flex justify-content-between"><span>Subtotal</span><strong>{{ $currencySymbol }}{{ number_format((float) $order->subtotal, 2) }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Discount</span><strong class="text-success">-{{ $currencySymbol }}{{ number_format((float) $order->discount_total, 2) }}</strong></div>
                    <hr>
                    <div class="d-flex justify-content-between"><span class="fw-semibold">Total</span><strong class="fs-5">{{ $currencySymbol }}{{ number_format((float) $order->total, 2) }}</strong></div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-success">Continue on WhatsApp</a>
            </div>
        </div>
    </div>
@endsection
