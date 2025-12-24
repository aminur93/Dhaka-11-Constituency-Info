<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HeroRequest extends FormRequest
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
       if ($this->isMethod('post')) {
            return $this->storeRules();
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->updateRules();
        }

        return [];
    }

    /**
     * Store rules
     */
    protected function storeRules(): array
    {
        return [
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_bn' => ['nullable', 'string', 'max:255'],

            'sub_title_en' => ['nullable', 'string', 'max:255'],
            'sub_title_bn' => ['nullable', 'string', 'max:255'],

            'description_en' => ['nullable', 'string'],
            'description_bn' => ['nullable', 'string'],

            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'status' => ['required', Rule::in([0, 1])],
        ];
    }

    /**
     * Update rules
     */
    protected function updateRules(): array
    {
        return [
            'title_en' => ['sometimes', 'nullable', 'string', 'max:255'],
            'title_bn' => ['sometimes', 'nullable', 'string', 'max:255'],

            'sub_title_en' => ['sometimes', 'nullable', 'string', 'max:255'],
            'sub_title_bn' => ['sometimes', 'nullable', 'string', 'max:255'],

            'description_en' => ['sometimes', 'nullable', 'string'],
            'description_bn' => ['sometimes', 'nullable', 'string'],

            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'status' => ['sometimes', Rule::in([0, 1])],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'image.required' => 'Hero image is required.',
            'image.image' => 'Uploaded file must be an image.',
            'image.mimes' => 'Image must be JPG, PNG, or WEBP format.',
            'image.max' => 'Image size must not exceed 2MB.',

            'status.in' => 'Status must be either active (1) or inactive (0).',
        ];
    }
}