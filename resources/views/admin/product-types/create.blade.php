@extends('layouts.admin')

@section('title', 'Create Product Type')

@section('content')
    <h1 class="h4 mb-3">Create Product Type</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.product-types.store') }}" method="POST">
                @csrf
                @include('admin.product-types.form', ['productType' => null])
            </form>
        </div>
    </div>
@endsection
