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
    // Keep the form frictionless for simple products without creating multiple accidental "No Size/No Color" variants.
    $variantRows = max(count($existingVariants), 1);
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
        <input type="file" id="fcMainImageInput" name="main_image" class="form-control" accept="image/*">
        <img
            src="{{ ($product?->image_thumb_url ?? $product?->main_image_url ?? asset(\App\Models\Catalog\Product::DEFAULT_PLACEHOLDER)) . ($product?->updated_at ? ('?v=' . $product->updated_at->timestamp) : '') }}"
            alt="Main image"
            id="fcMainImagePreview"
            class="img-thumbnail mt-2"
            style="max-height: 120px;"
        >
    </div>
    <div class="col-md-3">
        <label class="form-label">Gallery Images</label>
        <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
        <div id="gallery-color-map" class="mt-2 d-none">
            <div class="small text-muted mb-1">Assign a color to each uploaded image (optional)</div>
            <div class="d-grid gap-2" id="gallery-color-map-rows"></div>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">Short Description</label>
        <input type="text" name="short_description" class="form-control" maxlength="300" value="{{ old('short_description', $product?->short_description) }}">
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control">{{ old('description', $product?->description) }}</textarea>
    </div>
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
@error('variants')
    <div class="alert alert-danger py-2 mb-3">{{ $message }}</div>
@enderror
<div id="variants-container">
    @for($i = 0; $i < $variantRows; $i++)
        <div class="row g-2 border rounded p-2 mb-2 variant-row" data-index="{{ $i }}">
            <div class="col-md-2">
                <select name="variants[{{ $i }}][size_id]" class="form-select">
                    <option value="" disabled>Size</option>
                    <option value="__none__" @selected(
                        (string) data_get($existingVariants, "$i.size_id") === '__none__'
                        || data_get($existingVariants, "$i.size_id") === null
                        || data_get($existingVariants, "$i.size_id") === ''
                    )>No Size</option>
                    @foreach($sizes as $size)
                        <option value="{{ $size->id }}" @selected((string) data_get($existingVariants, "$i.size_id") === (string) $size->id)>{{ $size->name }}</option>
                    @endforeach
                </select>
                @error("variants.$i.size_id")
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-2">
                <select name="variants[{{ $i }}][color_id]" class="form-select">
                    <option value="" disabled>Color</option>
                    <option value="__none__" @selected(
                        (string) data_get($existingVariants, "$i.color_id") === '__none__'
                        || data_get($existingVariants, "$i.color_id") === null
                        || data_get($existingVariants, "$i.color_id") === ''
                    )>No Color</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}" @selected((string) data_get($existingVariants, "$i.color_id") === (string) $color->id)>{{ $color->name }}</option>
                    @endforeach
                </select>
                @error("variants.$i.color_id")
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
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
                <option value="" disabled>Size</option>
                <option value="__none__" selected>No Size</option>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="variants[__INDEX__][color_id]" class="form-select">
                <option value="" disabled>Color</option>
                <option value="__none__" selected>No Color</option>
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

        // Client-side integrity: block saving if an "intentional" row is incomplete.
        const form = container?.closest('form');
        const isFilled = (v) => {
            const s = String(v ?? '').trim();
            return s !== '';
        };
        const getVal = (row, selector) => row.querySelector(selector)?.value ?? '';
        const getNum = (row, selector) => parseInt(row.querySelector(selector)?.value ?? '0', 10) || 0;

        const clearRowErrors = (row) => {
            row.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        };

        const validateVariants = () => {
            const rows = Array.from(container?.querySelectorAll('.variant-row') ?? []);
            let hasError = false;

            rows.forEach((row) => {
                clearRowErrors(row);

                const size = getVal(row, 'select[name*="[size_id]"]');
                const color = getVal(row, 'select[name*="[color_id]"]');
                const sku = (row.querySelector('input[name*="[variant_sku]"]')?.value ?? '').trim();
                const priceOverride = (row.querySelector('input[name*="[price_override]"]')?.value ?? '').trim();
                const stock = getNum(row, 'input[name*="[stock_qty]"]');

                const isDefaultNone = (String(size) === '__none__') && (String(color) === '__none__');
                const intentional = (!isDefaultNone && (isFilled(size) || isFilled(color)))
                    || sku !== ''
                    || priceOverride !== ''
                    || stock > 0;
                if (!intentional) return;

                if (!isFilled(size)) {
                    hasError = true;
                    row.querySelector('select[name*="[size_id]"]')?.classList.add('is-invalid');
                }
                if (!isFilled(color)) {
                    hasError = true;
                    row.querySelector('select[name*="[color_id]"]')?.classList.add('is-invalid');
                }
            });

            return !hasError;
        };

        form?.addEventListener('submit', (e) => {
            if (!validateVariants()) {
                e.preventDefault();
                e.stopPropagation();
                window.scrollTo({ top: 0, behavior: 'smooth' });
                alert('Please complete all variant rows before saving the product.');
            }
        });
    })();
</script>

<script>
    (() => {
        const input = document.querySelector('input[name="gallery_images[]"]');
        const wrap = document.getElementById('gallery-color-map');
        const rows = document.getElementById('gallery-color-map-rows');
        if (!input || !wrap || !rows) return;

        const colors = @json(($colors ?? collect())->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values());

        const optionHtml = () => {
            const base = `<option value="">No color (default)</option>`;
            return base + colors.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
        };

        input.addEventListener('change', () => {
            rows.innerHTML = '';
            const files = Array.from(input.files || []);
            if (files.length === 0) {
                wrap.classList.add('d-none');
                return;
            }
            wrap.classList.remove('d-none');

            files.forEach((file, idx) => {
                const item = document.createElement('div');
                item.className = 'd-flex align-items-center justify-content-between gap-2 border rounded p-2 bg-light';
                item.innerHTML = `
                    <div class="small text-truncate" style="max-width: 55%;">${file.name}</div>
                    <select class="form-select form-select-sm" name="gallery_image_colors[${idx}]" style="max-width: 45%;">
                        ${optionHtml()}
                    </select>
                `;
                rows.appendChild(item);
            });
        });
    })();
</script>

<script>
    (() => {
        const input = document.getElementById('fcMainImageInput');
        const preview = document.getElementById('fcMainImagePreview');
        if (!input || !preview) return;

        let lastObjectUrl = null;
        input.addEventListener('change', () => {
            const file = input.files?.[0];
            if (!file) return;
            if (!String(file.type || '').startsWith('image/')) return;

            if (lastObjectUrl) URL.revokeObjectURL(lastObjectUrl);
            lastObjectUrl = URL.createObjectURL(file);
            preview.src = lastObjectUrl;
        });
    })();
</script>
