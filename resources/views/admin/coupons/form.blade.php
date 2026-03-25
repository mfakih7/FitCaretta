<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Coupon Code</label>
        <input type="text" name="code" class="form-control text-uppercase" value="{{ old('code', $coupon?->code) }}" required>
    </div>
    <div class="col-md-2">
        <label class="form-label">Type</label>
        <select name="type" class="form-select" required>
            <option value="percentage" @selected(old('type', $coupon?->type) === 'percentage')>Percentage</option>
            <option value="fixed" @selected(old('type', $coupon?->type) === 'fixed')>Fixed</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Value</label>
        <input type="number" step="0.01" min="0" name="value" class="form-control" value="{{ old('value', $coupon?->value) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Minimum Order Amount</label>
        <input type="number" step="0.01" min="0" name="minimum_order_amount" class="form-control" value="{{ old('minimum_order_amount', $coupon?->minimum_order_amount ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Usage Limit</label>
        <input type="number" min="1" name="usage_limit" class="form-control" value="{{ old('usage_limit', $coupon?->usage_limit) }}" placeholder="Unlimited if empty">
    </div>
    <div class="col-md-3">
        <label class="form-label">Per Customer Limit</label>
        <input type="number" min="1" name="usage_per_customer" class="form-control" value="{{ old('usage_per_customer', $coupon?->usage_per_customer) }}" placeholder="Unlimited if empty">
    </div>
    <div class="col-md-3">
        <label class="form-label">Start Date</label>
        <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date', $coupon?->start_date?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">End Date</label>
        <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date', $coupon?->end_date?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-select">
            <option value="1" @selected((string) old('is_active', $coupon?->is_active ?? 1) === '1')>Active</option>
            <option value="0" @selected((string) old('is_active', $coupon?->is_active ?? 1) === '0')>Inactive</option>
        </select>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
