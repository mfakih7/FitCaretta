<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductTypeUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productType = $this->route('product_type');

        return [
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('product_types', 'name')->ignore($productType?->id),
            ],
            'slug' => [
                'required',
                'string',
                'max:160',
                Rule::unique('product_types', 'slug')->ignore($productType?->id),
            ],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
