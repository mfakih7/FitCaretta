@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
    <h1 class="h4 mb-3">Edit Category</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            @include('admin.partials.image-guide', [
                'title' => 'Category Image Guide',
                'size' => '900 × 1200 px',
                'ratio' => '3:4',
                'bullets' => [
                    'Keep the subject centered.',
                    'Use clear lifestyle/category imagery.',
                    'Compose for cover display: strong centered subject, safe margins near edges (it may crop slightly).',
                    'Avoid inconsistent cropping or extra empty space.',
                    'Best result: category images look clean and premium in the ecommerce layout.',
                ],
            ])
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.categories.form', ['category' => $category])
            </form>
        </div>
    </div>
@endsection
