@extends('layouts.admin')

@section('title', 'Create Discount')

@section('content')
    <h1 class="h4 mb-3">Create Discount</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.discounts.store') }}">
                @csrf
                @include('admin.discounts.form', ['discount' => null])
            </form>
        </div>
    </div>
@endsection
