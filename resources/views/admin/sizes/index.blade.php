@extends('layouts.admin')

@section('title', 'Sizes')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Sizes</h1>
        <a href="{{ route('admin.sizes.create') }}" class="btn btn-primary">Add Size</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Sort</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($sizes as $size)
                    <tr>
                        <td>{{ $size->id }}</td>
                        <td>{{ $size->name }}</td>
                        <td>{{ $size->code }}</td>
                        <td>{{ $size->sort_order }}</td>
                        <td>
                            <span class="badge {{ $size->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $size->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.sizes.edit', $size) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.sizes.destroy', $size) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this size?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No sizes found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $sizes->links() }}
    </div>
@endsection
