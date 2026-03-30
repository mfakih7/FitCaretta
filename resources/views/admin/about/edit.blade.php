@extends('layouts.admin')

@section('title', 'About')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">About Page</h1>
            <div class="text-muted small">Edit the About content and control website visibility.</div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.about.update') }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-8">
                    <label class="form-label">Title (optional)</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $about?->title) }}" maxlength="160">
                    @error('title')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    @error('image')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror

                    @if($about?->image_path)
                        <img src="{{ asset('storage/' . $about->image_path) }}" alt="About image" class="img-thumbnail mt-2" style="max-height: 140px;">
                    @endif
                </div>

                <div class="col-12">
                    <label class="form-label">Story / About Text</label>
                    <textarea name="content" class="form-control" rows="8" placeholder="Write your store story...">{{ old('content', $about?->content) }}</textarea>
                    @error('content')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_enabled" id="is_enabled" value="1"
                               @checked((bool) old('is_enabled', $about?->is_enabled ?? false))>
                        <label class="form-check-label" for="is_enabled">
                            Show About page on website
                        </label>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Save</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.dashboard') }}">Back</a>
                </div>
            </form>
        </div>
    </div>
@endsection

