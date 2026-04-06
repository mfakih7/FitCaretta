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
            'page_title' => ['nullable', 'string', 'max:160'],
            'show_about_page' => ['nullable', 'boolean'],

            'section1_title' => ['nullable', 'string', 'max:160'],
            'section1_description' => ['nullable', 'string'],
            'section1_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'section2_title' => ['nullable', 'string', 'max:160'],
            'section2_description' => ['nullable', 'string'],
            'section2_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'section3_title' => ['nullable', 'string', 'max:160'],
            'section3_description' => ['nullable', 'string'],
            'section3_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}

