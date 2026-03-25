@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
    <h1 class="h4 mb-3">Create Category</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.categories.form', ['category' => null])
            </form>
        </div>
    </div>
@endsection
