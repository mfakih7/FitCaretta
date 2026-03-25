@extends('layouts.admin')

@section('title', 'Orders Report')

@section('content')
    @php
        $statusBadgeClass = [
            'pending' => 'bg-warning text-dark',
            'confirmed' => 'bg-primary',
            'delivered' => 'bg-success',
            'cancelled' => 'bg-danger',
            'processing' => 'bg-secondary',
            'failed' => 'bg-dark',
            'refunded' => 'bg-secondary',
        ];
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Orders Report</h1>
            @if($orders->total() > $orders->count())
                <div class="text-muted small">Showing {{ $orders->count() }} of {{ $orders->total() }}</div>
            @else
                <div class="text-muted small">{{ $orders->total() }} total</div>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary"
               href="{{ route('admin.reports.orders.export', request()->query()) }}">
                Export CSV
            </a>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total Orders</div>
                    <div class="h4 mb-0">{{ number_format((int) $summary['total_orders']) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Revenue Total</div>
                    <div class="h4 mb-0">
                        {{ config('store.currency_symbol') }}{{ number_format((float) $summary['revenue_total'], 2) }}
                    </div>
                    <div class="text-muted small">Confirmed + Delivered</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Confirmed Orders</div>
                    <div class="h4 mb-0">{{ number_format((int) $summary['confirmed_orders']) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Delivered Orders</div>
                    <div class="h4 mb-0">{{ number_format((int) $summary['delivered_orders']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.orders.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Order Number</label>
                    <input
                        type="text"
                        name="order_number"
                        value="{{ $filters['order_number'] }}"
                        class="form-control"
                        placeholder="FC-000123"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label small mb-1">Customer Name</label>
                    <input
                        type="text"
                        name="customer_name"
                        value="{{ $filters['customer_name'] }}"
                        class="form-control"
                        placeholder="Customer name..."
                    >
                </div>

                <div class="col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status }}" @selected($filters['status'] === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small mb-1">From</label>
                    <input
                        type="date"
                        name="from"
                        value="{{ $filters['from_raw'] }}"
                        class="form-control"
                    >
                </div>

                <div class="col-md-2">
                    <label class="form-label small mb-1">To</label>
                    <input
                        type="date"
                        name="to"
                        value="{{ $filters['to_raw'] }}"
                        class="form-control"
                    >
                </div>

                <div class="col-12 d-flex flex-wrap gap-2 pt-1">
                    <button type="submit" class="btn btn-primary">Apply</button>
                    <a href="{{ route('admin.reports.orders.index') }}" class="btn btn-outline-secondary">Reset</a>

                    <button type="submit" name="period" value="today" class="btn btn-outline-secondary">Today</button>
                    <button type="submit" name="period" value="week" class="btn btn-outline-secondary">This Week</button>
                    <button type="submit" name="period" value="month" class="btn btn-outline-secondary">This Month</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Product Info</th>
                    <th>Subtotal</th>
                    <th>Discount</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    @php
                        $date = $order->placed_at?->format('Y-m-d H:i') ?? $order->created_at?->format('Y-m-d H:i');
                        $status = strtolower((string) ($order->order_status ?? ''));
                        $badgeClass = $statusBadgeClass[$status] ?? 'bg-secondary';
                    @endphp

                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none">
                                {{ $order->order_number }}
                            </a>
                        </td>

                        <td class="text-muted small">{{ $date }}</td>

                        <td style="min-width:220px;">
                            <div>{{ $order->full_name }}</div>
                            <small class="text-muted">{{ $order->email }}</small>
                        </td>

                        <td>{{ $order->phone }}</td>

                        <td style="min-width:280px;">
                            <div class="border rounded bg-white" style="border-color:#eee; max-width:560px; max-height:140px; overflow:auto;">
                                <div class="d-flex justify-content-between px-2 py-1 small text-muted" style="background:#fafafa; border-bottom:1px solid #eee;">
                                    <span>Product</span>
                                    <span class="d-flex gap-3">
                                        <span>Variant</span>
                                        <span style="min-width:32px; text-align:right;">Qty</span>
                                    </span>
                                </div>

                                @forelse($order->items as $idx => $item)
                                    <div
                                        class="d-flex justify-content-between align-items-start px-2 py-1 small"
                                        @if($idx !== 0) style="border-top:1px solid #f1f1f1;" @endif
                                    >
                                        <div style="padding-right:10px;">
                                            <div class="fw-semibold">{{ $item->product_name }}</div>
                                        </div>

                                        <div class="d-flex gap-3 text-muted" style="white-space:nowrap;">
                                            <div>{{ $item->size_name ?? '-' }} / {{ $item->color_name ?? '-' }}</div>
                                            <div class="text-dark" style="min-width:32px; text-align:right;">x{{ $item->quantity }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-2 py-2 small text-muted">No items</div>
                                @endforelse
                            </div>
                        </td>

                        <td>{{ config('store.currency_symbol') }}{{ number_format((float) $order->subtotal, 2) }}</td>

                        <td class="text-success">
                            -{{ config('store.currency_symbol') }}{{ number_format((float) $order->discount_total, 2) }}
                        </td>

                        <td>
                            <strong>{{ config('store.currency_symbol') }}{{ number_format((float) $order->total, 2) }}</strong>
                        </td>

                        <td>
                            <span class="badge {{ $badgeClass }} text-capitalize">
                                {{ $order->order_status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No orders found for these filters.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $orders->links() }}
    </div>
@endsection