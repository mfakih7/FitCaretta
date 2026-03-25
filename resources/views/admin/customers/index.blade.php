@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Customers</h1>
            <div class="text-muted small">{{ $customers->total() }} total</div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small mb-1">Search by name</label>
                    <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Type a customer name...">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City / Area</th>
                    <th>Address</th>
                    <th>Created</th>
                </tr>
                </thead>
                <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->full_name }}</td>
                        <td>{{ $customer->email ?? '-' }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->city_area ?? '-' }}</td>
                        <td style="min-width:260px;">
                            <div class="text-truncate" style="max-width:420px;" title="{{ $customer->address ?? '' }}">
                                {{ $customer->address ?? '-' }}
                            </div>
                        </td>
                        <td class="text-muted small">{{ $customer->created_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No customers found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $customers->links() }}
    </div>
@endsection

