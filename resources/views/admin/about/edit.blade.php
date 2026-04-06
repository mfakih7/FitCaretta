@extends('layouts.admin')

@section('title', 'About Page')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">About Page</h1>
            <div class="text-muted small">General settings on top, then 3 sections.</div>
        </div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.dashboard') }}">Back</a>
    </div>

    @php
        $about = $about ?? null;
        $v = $about?->updated_at?->timestamp ?? time();
        $pageTitle = old('page_title', $about?->page_title ?? $about?->title ?? 'About us');
        $show = (bool) old('show_about_page', $about?->show_about_page ?? $about?->is_enabled ?? false);

        $s1Title = old('section1_title', $about?->section1_title ?? 'Our Story');
        $s2Title = old('section2_title', $about?->section2_title ?? 'Our Mission');
        $s3Title = old('section3_title', $about?->section3_title ?? 'Why Choose Us');

        $s1Desc = old('section1_description', $about?->section1_description ?? $about?->content ?? '');
        $s2Desc = old('section2_description', $about?->section2_description ?? '');
        $s3Desc = old('section3_description', $about?->section3_description ?? '');

        $s1Img = $about?->section1_image_path ?? $about?->image_path;
        $s2Img = $about?->section2_image_path;
        $s3Img = $about?->section3_image_path;
    @endphp

    <form method="POST" action="{{ route('admin.about.update') }}" enctype="multipart/form-data" class="d-flex flex-column gap-3">
        @csrf
        @method('PUT')

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">General</h2>
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">Page Title</label>
                        <input type="text" name="page_title" class="form-control" value="{{ $pageTitle }}" maxlength="160" placeholder="About us">
                        @error('page_title')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="show_about_page" id="show_about_page" value="1" @checked($show)>
                            <label class="form-check-label" for="show_about_page">Show About page on website</label>
                        </div>
                        @error('show_about_page')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Section 1</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Section 1 Title</label>
                        <input type="text" name="section1_title" class="form-control" value="{{ $s1Title }}" placeholder="Our Story">
                        @error('section1_title')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Section 1 Image</label>
                        <input type="file" name="section1_image" class="form-control" accept="image/*">
                        @error('section1_image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        @if($s1Img)
                            <img src="{{ asset('storage/' . $s1Img) }}?v={{ $v }}" alt="Section 1 image" class="img-thumbnail mt-2" style="max-height: 140px;">
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="form-label">Section 1 Description</label>
                        <textarea name="section1_description" class="form-control" rows="6">{{ $s1Desc }}</textarea>
                        @error('section1_description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Section 2</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Section 2 Title</label>
                        <input type="text" name="section2_title" class="form-control" value="{{ $s2Title }}" placeholder="Our Mission">
                        @error('section2_title')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Section 2 Image</label>
                        <input type="file" name="section2_image" class="form-control" accept="image/*">
                        @error('section2_image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        @if($s2Img)
                            <img src="{{ asset('storage/' . $s2Img) }}?v={{ $v }}" alt="Section 2 image" class="img-thumbnail mt-2" style="max-height: 140px;">
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="form-label">Section 2 Description</label>
                        <textarea name="section2_description" class="form-control" rows="6">{{ $s2Desc }}</textarea>
                        @error('section2_description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Section 3</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Section 3 Title</label>
                        <input type="text" name="section3_title" class="form-control" value="{{ $s3Title }}" placeholder="Why Choose Us">
                        @error('section3_title')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Section 3 Image</label>
                        <input type="file" name="section3_image" class="form-control" accept="image/*">
                        @error('section3_image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        @if($s3Img)
                            <img src="{{ asset('storage/' . $s3Img) }}?v={{ $v }}" alt="Section 3 image" class="img-thumbnail mt-2" style="max-height: 140px;">
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="form-label">Section 3 Description</label>
                        <textarea name="section3_description" class="form-control" rows="6">{{ $s3Desc }}</textarea>
                        @error('section3_description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.about.edit') }}">Reset</a>
        </div>
    </form>
@endsection

