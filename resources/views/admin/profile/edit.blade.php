@extends('layouts.admin')

@section('title', 'Profile')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Profile</h1>
            <div class="text-muted small">Update your account email and password.</div>
        </div>
    </div>

    @php
        $brandRel = 'assets/brand';
        $faviconRel = $brandRel . '/favicon.png';
        $logoRel = $brandRel . '/logo.png';
        $logoMarkRel = $brandRel . '/logo-mark.png';

        $faviconExists = is_file(public_path($faviconRel));
        $logoExists = is_file(public_path($logoRel));
        $logoMarkExists = is_file(public_path($logoMarkRel));

        $v = (string) time();
    @endphp

    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Profile Information</h2>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" value="{{ $user->role?->value ?? '-' }}" disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6"></div>

                    <div class="col-12">
                        <div class="text-muted small">Leave password blank if you don’t want to change it.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" autocomplete="new-password">
                        @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                    </div>
                </div>

                <hr class="my-4">

                <h2 class="h6 mb-1">Application Logos</h2>
                <div class="text-muted small mb-3">Upload PNG files to replace the current assets in <code>public/assets/brand</code>.</div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-white h-100">
                            <div class="fw-semibold mb-2">Favicon</div>
                            <div class="d-flex align-items-center justify-content-center border rounded" style="height:84px; background:#fafafa;">
                                @if($faviconExists)
                                    <img src="{{ asset($faviconRel) }}?v={{ $v }}" alt="Favicon preview" style="max-width:48px; max-height:48px;">
                                @else
                                    <div class="text-muted small">No file</div>
                                @endif
                            </div>
                            <div class="mt-2">
                                <input type="file" name="favicon" class="form-control" accept="image/png">
                                @error('favicon')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                <div class="form-text">PNG only. Max 2MB. Replaces <code>favicon.png</code>.</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-white h-100">
                            <div class="fw-semibold mb-2">Logo</div>
                            <div class="d-flex align-items-center justify-content-center border rounded" style="height:84px; background:#fafafa;">
                                @if($logoExists)
                                    <img src="{{ asset($logoRel) }}?v={{ $v }}" alt="Logo preview" style="max-width:160px; max-height:60px;">
                                @else
                                    <div class="text-muted small">No file</div>
                                @endif
                            </div>
                            <div class="mt-2">
                                <input type="file" name="logo" class="form-control" accept="image/png">
                                @error('logo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                <div class="form-text">PNG only. Max 2MB. Replaces <code>logo.png</code>.</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-white h-100">
                            <div class="fw-semibold mb-2">Logo Mark</div>
                            <div class="d-flex align-items-center justify-content-center border rounded" style="height:84px; background:#fafafa;">
                                @if($logoMarkExists)
                                    <img src="{{ asset($logoMarkRel) }}?v={{ $v }}" alt="Logo mark preview" style="max-width:80px; max-height:60px;">
                                @else
                                    <div class="text-muted small">No file</div>
                                @endif
                            </div>
                            <div class="mt-2">
                                <input type="file" name="logo_mark" class="form-control" accept="image/png">
                                @error('logo_mark')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                <div class="form-text">PNG only. Max 2MB. Replaces <code>logo-mark.png</code>.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </div>
    </form>
@endsection

