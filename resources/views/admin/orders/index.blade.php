@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
    @php($currencySymbol = config('store.currency_symbol', '$'))
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Orders</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>
                            <div>{{ $order->full_name }}</div>
                            <small class="text-muted">{{ $order->email }}</small>
                        </td>
                        <td>{{ $currencySymbol }}{{ number_format((float) $order->total, 2) }}</td>
                        <td><span class="badge bg-secondary text-capitalize">{{ $order->order_status }}</span></td>
                        <td>{{ $order->placed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No orders found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $orders->links() }}</div>
@endsection
