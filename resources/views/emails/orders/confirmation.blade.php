@include('emails.orders.receipt-layout', [
    'order' => $order,
    'title' => 'Order Received (Pending)',
    'subtitle' => 'We’ve received your order. It’s currently pending confirmation. We’ll email you when the status is updated.',
    'button_text' => 'View Order',
    'button_url' => route('orders.show', $order->public_token),
])
