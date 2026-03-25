@extends('layouts.admin')

@section('title', 'Create Coupon')

@section('content')
    <h1 class="h4 mb-3">Create Coupon</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.coupons.store') }}">
                @csrf
                @include('admin.coupons.form', ['coupon' => null])
            </form>
        </div>
    </div>
@endsection
