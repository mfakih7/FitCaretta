<?php

namespace App\Http\Requests\Admin;

use App\Models\Catalog\Discount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiscountStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:60', Rule::unique('discounts', 'code')],
            'type' => ['required', Rule::in([Discount::TYPE_PERCENTAGE, Discount::TYPE_FIXED])],
            'value' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'scope' => ['required', Rule::in([Discount::SCOPE_PRODUCT, Discount::SCOPE_CATEGORY, Discount::SCOPE_GLOBAL])],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'priority' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $type = $this->input('type');
            $value = (float) $this->input('value', 0);

            if ($type === Discount::TYPE_PERCENTAGE && $value > 100) {
                $validator->errors()->add('value', 'Percentage discount cannot exceed 100.');
            }

            if ($type === Discount::TYPE_FIXED && $value > 999999.99) {
                $validator->errors()->add('value', 'Fixed discount is above safe limit.');
            }

            if ($this->input('scope') === Discount::SCOPE_PRODUCT && empty($this->input('product_ids'))) {
                $validator->errors()->add('product_ids', 'Select at least one product for product scope.');
            }

            if ($this->input('scope') === Discount::SCOPE_CATEGORY && empty($this->input('category_ids'))) {
                $validator->errors()->add('category_ids', 'Select at least one category for category scope.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'priority' => $this->input('priority', 0),
        ]);
    }
}
