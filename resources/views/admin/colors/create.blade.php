@extends('layouts.admin')

@section('title', 'Create Color')

@section('content')
    <h1 class="h4 mb-3">Create Color</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.colors.store') }}" method="POST">
                @csrf
                @include('admin.colors.form', ['color' => null])
            </form>
        </div>
    </div>
@endsection
