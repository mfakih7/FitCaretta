@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
    <h1 class="h4 mb-3">Edit Category</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.categories.form', ['category' => $category])
            </form>
        </div>
    </div>
@endsection
