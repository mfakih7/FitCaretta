@extends('layouts.admin')

@section('title', 'Add Slide')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Add Slide</h1>
        <a href="{{ route('admin.homepage-slides.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.homepage-slides.store') }}" enctype="multipart/form-data" class="d-flex flex-column gap-3">
                @csrf
                @include('admin.homepage-slides.form', ['slide' => null])
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('admin.homepage-slides.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

