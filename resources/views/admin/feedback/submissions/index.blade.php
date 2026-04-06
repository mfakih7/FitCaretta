@extends('layouts.admin')

@section('title', 'Feedback Submissions')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Feedback Submissions</h1>
            @if($submissions->total() > $submissions->count())
                <div class="text-muted small">Showing {{ $submissions->count() }} of {{ $submissions->total() }}</div>
            @else
                <div class="text-muted small">{{ $submissions->total() }} total</div>
            @endif
        </div>
        <a href="{{ route('admin.feedback.settings.edit') }}" class="btn btn-sm btn-outline-secondary">Settings</a>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.feedback.submissions.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Subject, name, or email...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}" @selected(($status ?? '') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.feedback.submissions.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Subject</th>
                    <th>Page URL</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($submissions as $s)
                    <tr>
                        <td>#{{ $s->id }}</td>
                        <td>{{ $s->name }}</td>
                        <td class="text-muted">{{ $s->email }}</td>
                        <td>{{ $s->type?->name ?? '-' }}</td>
                        <td style="min-width:220px;">
                            <div class="fw-semibold">{{ $s->subject }}</div>
                        </td>
                        <td style="min-width:220px;">
                            @if($s->page_url)
                                <a href="{{ $s->page_url }}" target="_blank" class="text-decoration-none">{{ \Illuminate\Support\Str::limit($s->page_url, 40) }}</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary text-capitalize">{{ str_replace('_', ' ', $s->status) }}</span></td>
                        <td class="text-muted small">{{ $s->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.feedback.submissions.show', $s) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted">No feedback submissions yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $submissions->links() }}</div>
@endsection

