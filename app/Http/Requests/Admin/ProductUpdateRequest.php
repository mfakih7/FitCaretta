<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'product_type_id' => ['nullable', 'integer', 'exists:product_types,id'],
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('products', 'slug')->ignore($product?->id)],
            'sku' => ['required', 'string', 'max:80', Rule::unique('products', 'sku')->ignore($product?->id)],
            'short_description' => ['nullable', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'gender_target' => ['required', Rule::in(['men', 'women', 'unisex'])],
            'base_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lte:base_price'],
            'is_active' => ['required', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_new_arrival' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'main_image' => ['nullable', 'image', 'max:3072'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'max:3072'],
            'gallery_image_colors' => ['nullable', 'array'],
            'gallery_image_colors.*' => ['nullable', 'integer', 'exists:colors,id'],
            'variants' => ['nullable', 'array'],
            'variants.*.size_id' => ['nullable', 'integer', 'exists:sizes,id'],
            'variants.*.color_id' => ['nullable', 'integer', 'exists:colors,id'],
            'variants.*.variant_sku' => ['nullable', 'string', 'max:100'],
            'variants.*.price_override' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock_qty' => ['nullable', 'integer', 'min:0'],
            'variants.*.low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'variants.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $variantColorIds = collect($this->input('variants', []))
                ->pluck('color_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $mappedColorIds = collect($this->input('gallery_image_colors', []))
                ->filter(fn ($id) => $id !== null && $id !== '')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($mappedColorIds->isEmpty()) {
                return;
            }

            if ($variantColorIds->isEmpty()) {
                $validator->errors()->add('gallery_image_colors', 'Assigning image colors requires product variants with colors.');
                return;
            }

            $invalid = $mappedColorIds->diff($variantColorIds);
            if ($invalid->isNotEmpty()) {
                $validator->errors()->add('gallery_image_colors', 'One or more gallery image colors are not used by this product variants.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->filled('slug') ? $this->input('slug') : null,
            'is_active' => $this->boolean('is_active'),
            'is_featured' => $this->boolean('is_featured'),
            'is_new_arrival' => $this->boolean('is_new_arrival'),
        ]);
    }
}
