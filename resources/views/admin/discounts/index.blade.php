@extends('layouts.admin')

@section('title', 'Discounts')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Discounts</h1>
        <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary">Add Discount</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Scope</th>
                    <th>Date Range</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($discounts as $discount)
                    <tr>
                        <td>{{ $discount->name }}</td>
                        <td>{{ $discount->code ?? '-' }}</td>
                        <td class="text-capitalize">{{ $discount->type }}</td>
                        <td>{{ $discount->type === 'percentage' ? ($discount->value + 0).'%' : '$'.number_format($discount->value, 2) }}</td>
                        <td class="text-capitalize">{{ $discount->scope }}</td>
                        <td>
                            {{ $discount->start_date?->format('Y-m-d H:i') ?? 'Any' }}
                            <br>
                            {{ $discount->end_date?->format('Y-m-d H:i') ?? 'No end' }}
                        </td>
                        <td>
                            <span class="badge {{ $discount->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $discount->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.discounts.edit', $discount) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this discount?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No discounts found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $discounts->links() }}</div>
@endsection
