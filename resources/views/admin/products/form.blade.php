@php
    $existingVariants = old('variants', $product?->variants?->map(fn($v) => [
        'size_id' => $v->size_id,
        'color_id' => $v->color_id,
        'variant_sku' => $v->variant_sku,
        'price_override' => $v->price_override,
        'stock_qty' => $v->stock_qty,
        'low_stock_threshold' => $v->low_stock_threshold,
        'is_active' => $v->is_active,
    ])->toArray() ?? []);
    $variantRows = max(count($existingVariants), 3);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Name</label>
        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $product?->name) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Slug</label>
        <input type="text" id="slug" name="slug" class="form-control" value="{{ old('slug', $product?->slug) }}" placeholder="Auto-generated from name">
    </div>
    <div class="col-md-3">
        <label class="form-label">SKU</label>
        <input type="text" name="sku" class="form-control" value="{{ old('sku', $product?->sku) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
            <option value="">Select</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $product?->category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Product Type</label>
        <select name="product_type_id" class="form-select">
            <option value="">None</option>
            @foreach($productTypes as $type)
                <option value="{{ $type->id }}" @selected((string) old('product_type_id', $product?->product_type_id) === (string) $type->id)>{{ $type->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Gender Target</label>
        <select name="gender_target" class="form-select" required>
            <option value="men" @selected(old('gender_target', $product?->gender_target?->value) === 'men')>Men</option>
            <option value="women" @selected(old('gender_target', $product?->gender_target?->value) === 'women')>Women</option>
            <option value="unisex" @selected(old('gender_target', $product?->gender_target?->value) === 'unisex')>Unisex</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Base Price</label>
        <input type="number" step="0.01" min="0" name="base_price" class="form-control" value="{{ old('base_price', $product?->base_price) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Sale Price</label>
        <input type="number" step="0.01" min="0" name="sale_price" class="form-control" value="{{ old('sale_price', $product?->sale_price) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Main Image</label>
        <input type="file" name="main_image" class="form-control" accept="image/*">
        <img src="{{ $product?->main_image_url ?? asset(\App\Models\Catalog\Product::DEFAULT_PLACEHOLDER) }}" alt="Main image" class="img-thumbnail mt-2" style="max-height: 120px;">
    </div>
    <div class="col-md-3">
        <label class="form-label">Gallery Images</label>
        <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
    </div>
    <div class="col-12">
        <label class="form-label">Short Description</label>
        <input type="text" name="short_description" class="form-control" maxlength="300" value="{{ old('short_description', $product?->short_description) }}">
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control">{{ old('description', $product?->description) }}</textarea>
    </div>
    @if($product?->images?->count())
        <div class="col-12">
            <label class="form-label">Existing Gallery Images</label>
            <div class="d-flex flex-wrap gap-2">
                @foreach($product->images as $image)
                    <div class="border rounded p-1 bg-white">
                        <img src="{{ $image->image_url }}" alt="Gallery image" class="img-thumbnail d-block mb-1" style="height: 80px;">
                        <form method="POST" action="{{ route('admin.products.gallery-images.destroy', [$product, $image]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Delete this gallery image?')">Delete</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    <div class="col-md-6">
        <label class="form-label">Meta Title</label>
        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $product?->meta_title) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Meta Description</label>
        <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $product?->meta_description) }}">
    </div>
</div>

<hr class="my-4">
<h2 class="h5">Product Variants & Stock</h2>
<p class="text-muted small mb-3">Use rows for size/color combinations and stock quantities.</p>
<div id="variants-container">
    @for($i = 0; $i < $variantRows; $i++)
        <div class="row g-2 border rounded p-2 mb-2 variant-row" data-index="{{ $i }}">
            <div class="col-md-2">
                <select name="variants[{{ $i }}][size_id]" class="form-select">
                    <option value="">Size</option>
                    @foreach($sizes as $size)
                        <option value="{{ $size->id }}" @selected((string) data_get($existingVariants, "$i.size_id") === (string) $size->id)>{{ $size->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="variants[{{ $i }}][color_id]" class="form-select">
                    <option value="">Color</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}" @selected((string) data_get($existingVariants, "$i.color_id") === (string) $color->id)>{{ $color->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="variants[{{ $i }}][variant_sku]" class="form-control" placeholder="Variant SKU" value="{{ data_get($existingVariants, "$i.variant_sku") }}">
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" min="0" name="variants[{{ $i }}][price_override]" class="form-control" placeholder="Price Override" value="{{ data_get($existingVariants, "$i.price_override") }}">
            </div>
            <div class="col-md-1">
                <input type="number" min="0" name="variants[{{ $i }}][stock_qty]" class="form-control" placeholder="Stock" value="{{ data_get($existingVariants, "$i.stock_qty", 0) }}">
            </div>
            <div class="col-md-2">
                <input type="number" min="0" name="variants[{{ $i }}][low_stock_threshold]" class="form-control" placeholder="Low Stock" value="{{ data_get($existingVariants, "$i.low_stock_threshold", 5) }}">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger w-100 remove-variant">X</button>
                <input type="hidden" name="variants[{{ $i }}][is_active]" value="{{ data_get($existingVariants, "$i.is_active", 1) ? 1 : 0 }}">
            </div>
        </div>
    @endfor
</div>
<button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="add-variant-btn">Add Variant</button>

<hr class="my-4">
<div class="row g-3">
    <div class="col-md-3">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="is_active" value="1" id="is_active" @checked((bool) old('is_active', $product?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="is_featured" value="1" id="is_featured" @checked((bool) old('is_featured', $product?->is_featured ?? false))>
            <label class="form-check-label" for="is_featured">Featured</label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="is_new_arrival" value="1" id="is_new_arrival" @checked((bool) old('is_new_arrival', $product?->is_new_arrival ?? false))>
            <label class="form-check-label" for="is_new_arrival">New Arrival</label>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">Save Product</button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

<template id="variant-template">
    <div class="row g-2 border rounded p-2 mb-2 variant-row" data-index="__INDEX__">
        <div class="col-md-2">
            <select name="variants[__INDEX__][size_id]" class="form-select">
                <option value="">Size</option>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="variants[__INDEX__][color_id]" class="form-select">
                <option value="">Color</option>
                @foreach($colors as $color)
                    <option value="{{ $color->id }}">{{ $color->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="text" name="variants[__INDEX__][variant_sku]" class="form-control" placeholder="Variant SKU"></div>
        <div class="col-md-2"><input type="number" step="0.01" min="0" name="variants[__INDEX__][price_override]" class="form-control" placeholder="Price Override"></div>
        <div class="col-md-1"><input type="number" min="0" name="variants[__INDEX__][stock_qty]" class="form-control" placeholder="Stock" value="0"></div>
        <div class="col-md-2"><input type="number" min="0" name="variants[__INDEX__][low_stock_threshold]" class="form-control" placeholder="Low Stock" value="5"></div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger w-100 remove-variant">X</button>
            <input type="hidden" name="variants[__INDEX__][is_active]" value="1">
        </div>
    </div>
</template>

<script>
    (() => {
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        const slugify = (value) =>
            value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');

        if (nameInput && slugInput) {
            nameInput.addEventListener('input', () => {
                if (!slugInput.dataset.touched || slugInput.value.trim() === '') {
                    slugInput.value = slugify(nameInput.value);
                }
            });
            slugInput.addEventListener('input', () => {
                slugInput.dataset.touched = '1';
            });
        }

        const container = document.getElementById('variants-container');
        const template = document.getElementById('variant-template');
        const addBtn = document.getElementById('add-variant-btn');
        let index = container ? container.querySelectorAll('.variant-row').length : 0;

        const reindexRows = () => {
            const rows = container.querySelectorAll('.variant-row');
            rows.forEach((row, idx) => {
                row.dataset.index = idx;
                row.querySelectorAll('input, select, textarea').forEach((field) => {
                    const name = field.getAttribute('name');
                    if (!name) return;
                    field.setAttribute('name', name.replace(/variants\[\d+\]/, `variants[${idx}]`));
                });
            });
            index = rows.length;
        };

        addBtn?.addEventListener('click', () => {
            const html = template.innerHTML.replaceAll('__INDEX__', index++);
            container.insertAdjacentHTML('beforeend', html);
        });

        container?.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-variant')) {
                e.target.closest('.variant-row')?.remove();
                reindexRows();
            }
        });
    })();
</script>
