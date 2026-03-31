<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductGalleryImagesBatchUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image_colors' => ['required', 'array'],
            'image_colors.*' => ['nullable', 'integer', 'exists:colors,id'],
        ];
    }
}

