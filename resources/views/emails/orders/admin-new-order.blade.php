@include('emails.orders.receipt-layout', [
    'order' => $order,
    'title' => 'New Order Received',
    'subtitle' => 'A new order has been placed. Review the customer details and order summary below.',
    'button_text' => 'Confirm Order',
    'button_url' => route('admin.orders.show', $order->id),
    'show_link_copy' => false,
])

