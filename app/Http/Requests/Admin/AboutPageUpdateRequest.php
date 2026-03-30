<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AboutPageUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:160'],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_enabled' => ['nullable', 'boolean'],
        ];
    }
}

