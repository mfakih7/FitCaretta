<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $category?->name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control" value="{{ old('slug', $category?->slug) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Parent Category</label>
        <select name="parent_id" class="form-select">
            <option value="">None</option>
            @foreach($parentCategories as $parent)
                <option value="{{ $parent->id }}" @selected((string) old('parent_id', $category?->parent_id) === (string) $parent->id)>
                    {{ $parent->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $category?->sort_order ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-select">
            <option value="1" @selected((string) old('is_active', $category?->is_active ?? 1) === '1')>Active</option>
            <option value="0" @selected((string) old('is_active', $category?->is_active ?? 1) === '0')>Inactive</option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Image Path</label>
        <input type="text" name="image_path" class="form-control" value="{{ old('image_path', $category?->image_path) }}" placeholder="storage/categories/example.jpg">
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control">{{ old('description', $category?->description) }}</textarea>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
