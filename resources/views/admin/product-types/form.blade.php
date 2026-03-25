<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $productType?->name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control" value="{{ old('slug', $productType?->slug) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-select">
            <option value="1" @selected((string) old('is_active', $productType?->is_active ?? 1) === '1')>Active</option>
            <option value="0" @selected((string) old('is_active', $productType?->is_active ?? 1) === '0')>Inactive</option>
        </select>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('admin.product-types.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
