@extends('layouts.admin')

@section('title', 'Coupons')

@section('content')
    @php($currencySymbol = config('store.currency_symbol', '$'))
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Coupons</h1>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">Add Coupon</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Min Order</th>
                    <th>Usage</th>
                    <th>Date Range</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td>{{ $coupon->code }}</td>
                        <td class="text-capitalize">{{ $coupon->type }}</td>
                        <td>{{ $coupon->type === 'percentage' ? ($coupon->value + 0).'%' : $currencySymbol . number_format($coupon->value, 2) }}</td>
                        <td>{{ $currencySymbol }}{{ number_format($coupon->minimum_order_amount, 2) }}</td>
                        <td>
                            Used: {{ $coupon->used_count }}
                            <br>
                            Limit: {{ $coupon->usage_limit ?? 'Unlimited' }}
                        </td>
                        <td>
                            {{ $coupon->start_date?->format('Y-m-d H:i') ?? 'Any' }}
                            <br>
                            {{ $coupon->end_date?->format('Y-m-d H:i') ?? 'No end' }}
                        </td>
                        <td>
                            <span class="badge {{ $coupon->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this coupon?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No coupons found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $coupons->links() }}</div>
@endsection
