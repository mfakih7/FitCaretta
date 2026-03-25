@extends('layouts.admin')

@section('title', 'Colors')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Colors</h1>
        <a href="{{ route('admin.colors.create') }}" class="btn btn-primary">Add Color</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Hex</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($colors as $color)
                    <tr>
                        <td>{{ $color->id }}</td>
                        <td>{{ $color->name }}</td>
                        <td>{{ $color->code ?? '-' }}</td>
                        <td>
                            @if($color->hex_code)
                                <span class="d-inline-flex align-items-center gap-2">
                                    <span class="border rounded-circle d-inline-block" style="width: 14px; height: 14px; background: {{ $color->hex_code }};"></span>
                                    {{ $color->hex_code }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $color->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $color->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.colors.edit', $color) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.colors.destroy', $color) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this color?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No colors found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $colors->links() }}
    </div>
@endsection
