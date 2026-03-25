@extends('layouts.admin-auth')

@section('title', 'Admin Login')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="text-center mb-2">
                <img src="{{ asset(config('store.brand.logo_dark_path')) }}" alt="{{ config('store.brand.logo_alt') }}" style="height:34px;width:auto;">
            </div>
            <h1 class="h4 mb-3 text-center">{{ config('store.admin_name') }}</h1>

            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>

                <button class="btn btn-dark w-100" type="submit">Login</button>
            </form>
        </div>
    </div>
@endsection
