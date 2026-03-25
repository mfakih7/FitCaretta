@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
    <h1 class="h4 mb-3">Create Product</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.products.form', ['product' => null])
            </form>
        </div>
    </div>
@endsection
