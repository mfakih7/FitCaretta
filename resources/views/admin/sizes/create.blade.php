@extends('layouts.admin')

@section('title', 'Create Size')

@section('content')
    <h1 class="h4 mb-3">Create Size</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.sizes.store') }}" method="POST">
                @csrf
                @include('admin.sizes.form', ['size' => null])
            </form>
        </div>
    </div>
@endsection
