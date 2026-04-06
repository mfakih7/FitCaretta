@extends('layouts.frontend')

@section('title', 'Feedback - ' . config('store.name'))

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-2">Feedback</h1>
        <p class="text-muted mb-0">Report a bug, suggestion, or issue during testing.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('feedback.store') }}" enctype="multipart/form-data" class="row g-3">
                @csrf

                <input type="hidden" name="page_url" value="{{ old('page_url', url()->previous()) }}">

                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Type</label>
                    <select name="feedback_type_id" class="form-select" required>
                        <option value="">Select type...</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" @selected((string) old('feedback_type_id') === (string) $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('feedback_type_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
                    @error('subject')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Message / Description</label>
                    <textarea name="message" class="form-control" rows="6" placeholder="Describe the issue, steps to reproduce, expected behavior..." required>{{ old('message') }}</textarea>
                    @error('message')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Screenshot (optional)</label>
                    <input type="file" name="screenshot" class="form-control" accept="image/*">
                    @error('screenshot')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    <div class="form-text">JPG/PNG/WEBP up to 5MB.</div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-dark px-4">Submit Feedback</button>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

