@extends('layouts.admin')

@section('title', 'Edit Discount')

@section('content')
    <h1 class="h4 mb-3">Edit Discount</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.discounts.update', $discount) }}">
                @csrf
                @method('PUT')
                @include('admin.discounts.form', ['discount' => $discount])
            </form>
        </div>
    </div>
@endsection
