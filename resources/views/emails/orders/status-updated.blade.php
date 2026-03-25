@php
    $label = ucfirst($newStatus);
    $subtitle = "Your order status has been updated to {$label}.";
    if ($newStatus === 'confirmed') {
        $subtitle = 'Good news — your order has been confirmed and is now being prepared.';
    } elseif ($newStatus === 'cancelled') {
        $subtitle = 'Your order has been cancelled. If you believe this is a mistake, please contact us.';
    } elseif ($newStatus === 'delivered') {
        $subtitle = 'Your order has been delivered. Thank you for shopping with us.';
    } elseif ($newStatus === 'pending') {
        $subtitle = 'Your order is currently pending confirmation.';
    }
@endphp

@include('emails.orders.receipt-layout', [
    'order' => $order,
    'title' => "Order {$label}",
    'subtitle' => $subtitle,
    'button_text' => 'View Order',
    'button_url' => route('orders.show', $order->public_token),
])

