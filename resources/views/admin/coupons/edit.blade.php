@extends('layouts.admin')

@section('title', 'Edit Coupon')

@section('content')
    <h1 class="h4 mb-3">Edit Coupon</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
                @csrf
                @method('PUT')
                @include('admin.coupons.form', ['coupon' => $coupon])
            </form>
        </div>
    </div>
@endsection
