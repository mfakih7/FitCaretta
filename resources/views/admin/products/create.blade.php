@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
    <h1 class="h4 mb-3">Create Product</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            @include('admin.partials.image-guide', [
                'title' => 'Product Image Guide',
                'size' => '800 × 1000 px',
                'ratio' => '4:5',
                'bullets' => [
                    'Keep the product centered within the canvas (balanced spacing on all sides).',
                    'Avoid over-zoomed photos: aim for the product to occupy ~82%–85% of the frame.',
                    'Keep a consistent background and framing across the catalog.',
                    'Always use 4:5 ratio (avoid different image ratios).',
                ],
            ])
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.products.form', ['product' => null])
            </form>
        </div>
    </div>
@endsection
