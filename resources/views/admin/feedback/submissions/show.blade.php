@extends('layouts.admin')

@section('title', 'Feedback #' . $submission->id)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Feedback #{{ $submission->id }}</h1>
            <div class="text-muted small">{{ $submission->created_at?->format('Y-m-d H:i') }}</div>
        </div>
        <a href="{{ route('admin.feedback.submissions.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small">Subject</div>
                            <div class="h5 mb-1">{{ $submission->subject }}</div>
                            <div class="text-muted small">Type: <strong class="text-dark">{{ $submission->type?->name ?? '-' }}</strong></div>
                        </div>
                        <span class="badge bg-secondary text-capitalize">{{ str_replace('_', ' ', $submission->status) }}</span>
                    </div>

                    <hr>

                    <div class="text-muted small mb-1">Message</div>
                    <div style="white-space: pre-line;">{{ $submission->message }}</div>

                    @if($submission->page_url)
                        <hr>
                        <div class="text-muted small mb-1">Page URL</div>
                        <a href="{{ $submission->page_url }}" target="_blank" class="text-decoration-none">{{ $submission->page_url }}</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="text-muted small">Reporter</div>
                    <div class="fw-semibold">{{ $submission->name }}</div>
                    <div class="text-muted">{{ $submission->email }}</div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.feedback.submissions.status', $submission) }}" class="row g-2 align-items-end">
                        @csrf
                        @method('PATCH')
                        <div class="col-12">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach($statusOptions as $key => $label)
                                    <option value="{{ $key }}" @selected($submission->status === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-primary" type="submit">Update Status</button>
                            <a href="{{ route('admin.feedback.submissions.show', $submission) }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small mb-2">Screenshot</div>
                    @if($submission->screenshot_url)
                        <a href="{{ $submission->screenshot_url }}" target="_blank" class="d-block">
                            <img src="{{ $submission->screenshot_url }}?v={{ $submission->updated_at?->timestamp }}" alt="Screenshot" class="img-fluid rounded border">
                        </a>
                    @else
                        <div class="text-muted small">No screenshot</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

