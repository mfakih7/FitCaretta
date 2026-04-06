<?php

namespace App\Http\Requests\Frontend;

use App\Models\FeedbackType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeedbackSubmissionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'feedback_type_id' => [
                'required',
                'integer',
                Rule::exists('feedback_types', 'id')->where(fn ($q) => $q->where('is_active', true)),
            ],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:5000'],
            'screenshot' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'page_url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'page_url' => (string) ($this->input('page_url') ?? $this->headers->get('referer', '')),
        ]);
    }
}

