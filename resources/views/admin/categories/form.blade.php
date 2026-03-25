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
        @php
            $existingPath = (string) old('image_path', $category?->image_path);
            $existingUrl = $category?->image_url ?? asset(\App\Models\Catalog\Category::DEFAULT_PLACEHOLDER);
        @endphp
        <label class="form-label">Category Image</label>
        <input type="file" name="image" class="form-control" accept="image/*" id="category-image-input">
        @error('image')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
        <div class="form-text">PNG/JPG/WEBP recommended. If you don’t upload a new one, the existing image stays.</div>

        <div class="mt-2">
            <div class="small text-muted mb-1">Preview</div>
            <img
                id="category-image-preview"
                src="{{ $existingUrl }}"
                alt="Category image preview"
                style="width:140px;height:180px;object-fit:contain;object-position:center;border:1px solid #e5e7eb;background:#fafafa;padding:8px;"
            >
        </div>

        <div class="mt-3">
            <label class="form-label">Image Path (legacy/manual)</label>
            <input type="text" name="image_path" class="form-control" value="{{ $existingPath }}" placeholder="storage/categories/example.jpg or images/placeholders/..."
            >
            @error('image_path')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
            <div class="form-text">Backward compatible: you can still paste an existing path/URL. Uploading a file will override this value.</div>
        </div>
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

<script>
    (() => {
        const input = document.getElementById('category-image-input');
        const preview = document.getElementById('category-image-preview');
        if (!input || !preview) return;

        input.addEventListener('change', () => {
            const file = input.files && input.files[0];
            if (!file) return;

            const url = URL.createObjectURL(file);
            preview.src = url;
        });
    })();
</script>
