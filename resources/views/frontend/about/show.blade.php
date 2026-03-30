@extends('layouts.frontend')

@section('title', 'About')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-2">{{ $about->title ?: 'About' }}</h1>
        <p class="text-muted mb-0">{{ config('store.short_description') }}</p>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-5">
            @if($about->image_path)
                <img src="{{ asset('storage/' . $about->image_path) }}" class="img-fluid rounded border bg-white" alt="{{ $about->title ?: 'About' }}">
            @else
                <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="height:320px;">
                    No image
                </div>
            @endif
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted" style="white-space: pre-line;">{{ $about->content }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection

