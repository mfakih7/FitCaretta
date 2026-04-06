@extends('layouts.admin')

@section('title', 'Feedback')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Feedback</h1>
            <div class="text-muted small">Enable feedback, choose which types appear on the website.</div>
        </div>
        <a href="{{ route('admin.feedback.submissions.index') }}" class="btn btn-sm btn-outline-secondary">View Submissions</a>
    </div>

    <form method="POST" action="{{ route('admin.feedback.settings.update') }}" class="d-flex flex-column gap-3">
        @csrf
        @method('PUT')

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Global</h2>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_enabled" name="is_enabled" value="1"
                           @checked((bool) old('is_enabled', $settings->is_enabled))>
                    <label class="form-check-label" for="is_enabled">Enable Feedback Feature</label>
                </div>
                @error('is_enabled')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <div class="form-text">When disabled, the Feedback menu and page are hidden from the website.</div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 mb-0">Feedback Types</h2>
                </div>
                <div class="text-muted small mb-3">Only checked types will show in the website dropdown.</div>

                <div class="row g-2">
                    @foreach($types as $type)
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="types[]" id="type_{{ $type->id }}" value="{{ $type->id }}"
                                       @checked(in_array($type->id, (array) old('types', $types->where('is_active', true)->pluck('id')->all()), true))>
                                <label class="form-check-label" for="type_{{ $type->id }}">{{ $type->name }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('types')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.feedback.settings.edit') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
@endsection

