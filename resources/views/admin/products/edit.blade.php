@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
    <h1 class="h4 mb-3">Edit Product</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.products.form', ['product' => $product])
            </form>

            @include('admin.products.gallery-images', ['product' => $product])
        </div>
    </div>
@endsection
