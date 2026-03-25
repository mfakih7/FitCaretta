<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ColorUpdateRequest extends FormRequest
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
        $color = $this->route('color');

        return [
            'name' => [
                'required',
                'string',
                'max:80',
                Rule::unique('colors', 'name')
                    ->where(fn ($q) => $q->where('code', $this->input('code')))
                    ->ignore($color?->id),
            ],
            'code' => ['nullable', 'string', 'max:30'],
            'hex_code' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('hex_code')) {
            $this->merge(['hex_code' => strtoupper($this->input('hex_code'))]);
        }
    }
}
