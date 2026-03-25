<?php

namespace App\Http\Requests\Admin;

use App\Models\Catalog\Coupon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $coupon = $this->route('coupon');

        return [
            'code' => ['required', 'string', 'max:60', Rule::unique('coupons', 'code')->ignore($coupon?->id)],
            'type' => ['required', Rule::in([Coupon::TYPE_PERCENTAGE, Coupon::TYPE_FIXED])],
            'value' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'minimum_order_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'usage_per_customer' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $type = $this->input('type');
            $value = (float) $this->input('value', 0);

            if ($type === Coupon::TYPE_PERCENTAGE && $value > 100) {
                $validator->errors()->add('value', 'Percentage discount cannot exceed 100.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'minimum_order_amount' => $this->input('minimum_order_amount', 0),
        ]);
    }
}
