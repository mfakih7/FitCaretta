<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937;">
@php($currencySymbol = config('store.currency_symbol', '$'))
<p style="margin:0 0 12px;">
    <img src="{{ asset(config('store.brand.logo_primary_path')) }}" alt="{{ config('store.brand.logo_alt') }}" style="height:32px;width:auto;">
</p>
<h2>Thank you for your order, {{ $order->full_name }}!</h2>
<p>Your order <strong>{{ $order->order_number }}</strong> has been received.</p>

<table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; margin-top: 12px;">
    <thead>
    <tr>
        <th align="left">Product</th>
        <th align="left">Variant</th>
        <th align="center">Qty</th>
        <th align="right">Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($order->items as $item)
        <tr>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->size_name ?? '-' }} / {{ $item->color_name ?? '-' }}</td>
            <td align="center">{{ $item->quantity }}</td>
            <td align="right">{{ $currencySymbol }}{{ number_format((float) $item->line_total, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p style="margin-top: 14px;">
    Subtotal: <strong>{{ $currencySymbol }}{{ number_format((float) $order->subtotal, 2) }}</strong><br>
    Discount: <strong>{{ $currencySymbol }}{{ number_format((float) $order->discount_total, 2) }}</strong><br>
    Total: <strong>{{ $currencySymbol }}{{ number_format((float) $order->total, 2) }}</strong>
</p>

<p>
    View your order details:
    <a href="{{ route('orders.show', $order->public_token) }}">{{ route('orders.show', $order->public_token) }}</a>
</p>

<p>{{ config('store.name') }} Team</p>
</body>
</html>
