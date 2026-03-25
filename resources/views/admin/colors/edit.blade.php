@extends('layouts.admin')

@section('title', 'Edit Color')

@section('content')
    <h1 class="h4 mb-3">Edit Color</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.colors.update', $color) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.colors.form', ['color' => $color])
            </form>
        </div>
    </div>
@endsection
