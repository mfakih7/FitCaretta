@extends('layouts.admin')

@section('title', 'Edit Size')

@section('content')
    <h1 class="h4 mb-3">Edit Size</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.sizes.update', $size) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.sizes.form', ['size' => $size])
            </form>
        </div>
    </div>
@endsection
