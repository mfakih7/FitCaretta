@extends('layouts.admin')

@section('title', 'Edit Slide')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Edit Slide</h1>
            <div class="text-muted small">ID: {{ $slide->id }}</div>
        </div>
        <a href="{{ route('admin.homepage-slides.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.homepage-slides.update', $slide) }}" enctype="multipart/form-data" class="d-flex flex-column gap-3">
                @csrf
                @method('PUT')
                @include('admin.homepage-slides.form', ['slide' => $slide])
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.homepage-slides.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

