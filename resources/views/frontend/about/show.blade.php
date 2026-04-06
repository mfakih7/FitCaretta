@extends('layouts.frontend')

@section('title', ($about->page_title ?? $about->title ?? 'About us') . ' - ' . config('store.name'))

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-2">{{ $about->page_title ?? $about->title ?? 'About us' }}</h1>
        <p class="text-muted mb-0">{{ config('store.short_description') }}</p>
    </div>

    @php
        $v = $about->updated_at?->timestamp ?? time();

        $sections = [
            [
                'title' => $about->section1_title ?? 'Our Story',
                'description' => $about->section1_description ?? $about->content ?? '',
                'image_path' => $about->section1_image_path ?? $about->image_path,
            ],
            [
                'title' => $about->section2_title ?? 'Our Mission',
                'description' => $about->section2_description ?? '',
                'image_path' => $about->section2_image_path,
            ],
            [
                'title' => $about->section3_title ?? 'Why Choose Us',
                'description' => $about->section3_description ?? '',
                'image_path' => $about->section3_image_path,
            ],
        ];
    @endphp

    <div class="d-flex flex-column gap-4">
        @foreach($sections as $idx => $section)
            @php
                $hasImage = filled($section['image_path']);
                $hasText = filled($section['description']) || filled($section['title']);
                if (! $hasImage && ! $hasText) continue;
                $reverse = $idx % 2 === 1;
            @endphp

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-5 @if($reverse) order-lg-2 @endif">
                            @if($hasImage)
                                <div class="fc-media rounded border" style="aspect-ratio: 4 / 3; background: var(--fc-soft-bg);">
                                    <img src="{{ asset('storage/' . $section['image_path']) }}?v={{ $v }}" alt="{{ $section['title'] }}" style="object-fit:cover;">
                                </div>
                            @else
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="aspect-ratio: 4 / 3;">
                                    <div class="text-muted small">No image</div>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-7 @if($reverse) order-lg-1 @endif">
                            <div class="fc-eyebrow mb-2">About</div>
                            <h2 class="h4 mb-2">{{ $section['title'] }}</h2>
                            <div class="text-muted" style="white-space: pre-line;">{{ $section['description'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

