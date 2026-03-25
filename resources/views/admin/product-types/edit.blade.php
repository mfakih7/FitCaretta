@extends('layouts.admin')

@section('title', 'Edit Product Type')

@section('content')
    <h1 class="h4 mb-3">Edit Product Type</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.product-types.update', $productType) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.product-types.form', ['productType' => $productType])
            </form>
        </div>
    </div>
@endsection
