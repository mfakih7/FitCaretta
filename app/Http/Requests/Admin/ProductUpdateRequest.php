<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\Catalog\Size;
use App\Models\Catalog\Color;

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
            'main_image' => ['nullable', 'image', 'max:3072', 'dimensions:ratio=4/5,min_width=800,min_height=1000'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'max:3072', 'dimensions:ratio=4/5,min_width=800,min_height=1000'],
            'gallery_image_colors' => ['nullable', 'array'],
            'gallery_image_colors.*' => ['nullable', 'integer', 'exists:colors,id'],
            'variants' => ['nullable', 'array'],
            'variants.*.size_id' => ['nullable'],
            'variants.*.color_id' => ['nullable'],
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
            $variants = collect($this->input('variants', []))->values();
            $hasIncomplete = false;

            $validSizeIds = Size::query()->pluck('id')->map(fn ($v) => (string) $v)->all();
            $validColorIds = Color::query()->pluck('id')->map(fn ($v) => (string) $v)->all();

            foreach ($variants as $i => $row) {
                $sizeId = data_get($row, 'size_id');
                $colorId = data_get($row, 'color_id');
                $sku = trim((string) data_get($row, 'variant_sku', ''));
                $priceOverride = data_get($row, 'price_override');
                $stockQty = (int) data_get($row, 'stock_qty', 0);

                $touched = filled($sizeId)
                    || filled($colorId)
                    || $sku !== ''
                    || ($priceOverride !== null && $priceOverride !== '')
                    || $stockQty > 0;

                if (! $touched) {
                    continue;
                }

                if (! filled($sizeId)) {
                    $hasIncomplete = true;
                    $validator->errors()->add("variants.$i.size_id", 'Size is required for each variant.');
                } elseif ((string) $sizeId !== '__none__' && ! in_array((string) $sizeId, $validSizeIds, true)) {
                    $hasIncomplete = true;
                    $validator->errors()->add("variants.$i.size_id", 'Selected size is invalid.');
                }
                if (! filled($colorId)) {
                    $hasIncomplete = true;
                    $validator->errors()->add("variants.$i.color_id", 'Color is required for each variant.');
                } elseif ((string) $colorId !== '__none__' && ! in_array((string) $colorId, $validColorIds, true)) {
                    $hasIncomplete = true;
                    $validator->errors()->add("variants.$i.color_id", 'Selected color is invalid.');
                }
            }

            if ($hasIncomplete) {
                $validator->errors()->add('variants', 'Please complete all variant rows before saving the product.');
            }

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

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        if (! is_array($data)) {
            return $data;
        }

        if (isset($data['variants']) && is_array($data['variants'])) {
            $data['variants'] = collect($data['variants'])->map(function ($v) {
                if (! is_array($v)) return $v;
                if (($v['size_id'] ?? null) === '__none__') $v['size_id'] = null;
                if (($v['color_id'] ?? null) === '__none__') $v['color_id'] = null;
                return $v;
            })->values()->all();
        }

        return $data;
    }
}
