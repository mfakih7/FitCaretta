@php
    $selectedProducts = old('product_ids', $discount?->products?->pluck('id')->toArray() ?? []);
    $selectedCategories = old('category_ids', $discount?->categories?->pluck('id')->toArray() ?? []);
    $scopeValue = old('scope', $discount?->scope ?? 'global');
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $discount?->name) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Code (optional)</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $discount?->code) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">Type</label>
        <select name="type" class="form-select" required>
            <option value="percentage" @selected(old('type', $discount?->type) === 'percentage')>Percentage</option>
            <option value="fixed" @selected(old('type', $discount?->type) === 'fixed')>Fixed</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Value</label>
        <input type="number" step="0.01" min="0" name="value" class="form-control" value="{{ old('value', $discount?->value) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Scope</label>
        <select name="scope" id="scope" class="form-select" required>
            <option value="global" @selected($scopeValue === 'global')>Global</option>
            <option value="product" @selected($scopeValue === 'product')>Product-specific</option>
            <option value="category" @selected($scopeValue === 'category')>Category-specific</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Priority</label>
        <input type="number" min="0" name="priority" class="form-control" value="{{ old('priority', $discount?->priority ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Start Date</label>
        <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date', $discount?->start_date?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">End Date</label>
        <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date', $discount?->end_date?->format('Y-m-d\TH:i')) }}">
    </div>

    <div class="col-12" id="product_scope_fields">
        <label class="form-label">Products</label>
        <select name="product_ids[]" class="form-select" multiple size="6">
            @foreach($products as $product)
                <option value="{{ $product->id }}" @selected(in_array($product->id, $selectedProducts))>{{ $product->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12" id="category_scope_fields">
        <label class="form-label">Categories</label>
        <select name="category_ids[]" class="form-select" multiple size="6">
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(in_array($category->id, $selectedCategories))>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-select">
            <option value="1" @selected((string) old('is_active', $discount?->is_active ?? 1) === '1')>Active</option>
            <option value="0" @selected((string) old('is_active', $discount?->is_active ?? 1) === '0')>Inactive</option>
        </select>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

<script>
    (() => {
        const scope = document.getElementById('scope');
        const productFields = document.getElementById('product_scope_fields');
        const categoryFields = document.getElementById('category_scope_fields');

        const toggleScopeFields = () => {
            productFields.style.display = scope.value === 'product' ? 'block' : 'none';
            categoryFields.style.display = scope.value === 'category' ? 'block' : 'none';
        };

        toggleScopeFields();
        scope.addEventListener('change', toggleScopeFields);
    })();
</script>
