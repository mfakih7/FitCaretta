<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $color?->name) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Code</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $color?->code) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Hex Code</label>
        <input type="text" name="hex_code" class="form-control" placeholder="#000000" value="{{ old('hex_code', $color?->hex_code) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-select">
            <option value="1" @selected((string) old('is_active', $color?->is_active ?? 1) === '1')>Active</option>
            <option value="0" @selected((string) old('is_active', $color?->is_active ?? 1) === '0')>Inactive</option>
        </select>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('admin.colors.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
