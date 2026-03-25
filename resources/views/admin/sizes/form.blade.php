<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $size?->name) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Code</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $size?->code) }}" required>
    </div>
    <div class="col-md-2">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $size?->sort_order ?? 0) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-select">
            <option value="1" @selected((string) old('is_active', $size?->is_active ?? 1) === '1')>Active</option>
            <option value="0" @selected((string) old('is_active', $size?->is_active ?? 1) === '0')>Inactive</option>
        </select>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('admin.sizes.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
