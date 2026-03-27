@extends('layouts.admin')

@section('title', 'Homepage Slides')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Homepage Slides</h1>
            @if($slides->total() > $slides->count())
                <div class="text-muted small">Showing {{ $slides->count() }} of {{ $slides->total() }}</div>
            @else
                <div class="text-muted small">{{ $slides->total() }} total</div>
            @endif
        </div>
        <a href="{{ route('admin.homepage-slides.create') }}" class="btn btn-primary">Add Slide</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                <tr>
                    <th style="width:90px;">Image</th>
                    <th>Title</th>
                    <th>Badge</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($slides as $slide)
                    <tr>
                        <td>
                            @if($slide->image_url)
                                <img src="{{ $slide->image_url }}?v={{ $slide->updated_at?->timestamp }}" alt="Slide image" class="rounded border" style="width:72px;height:48px;object-fit:cover;">
                            @else
                                <div class="text-muted small">No image</div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $slide->title ?: '-' }}</div>
                            <div class="text-muted small text-truncate" style="max-width:520px;">{{ $slide->subtitle }}</div>
                        </td>
                        <td class="text-muted">{{ $slide->badge ?: '-' }}</td>
                        <td>
                            <span class="badge {{ $slide->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $slide->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $slide->sort_order }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.homepage-slides.edit', $slide) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.homepage-slides.destroy', $slide) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this slide?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No slides yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $slides->links() }}</div>
@endsection

