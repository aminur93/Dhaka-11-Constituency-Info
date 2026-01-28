<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IssueCategoryRequest extends FormRequest
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
            'name_en' => ['required', 'string', 'max:255'],
            'name_bn' => ['nullable', 'string', 'max:255'],

            'description_en' => ['nullable', 'string'],
            'description_bn' => ['nullable', 'string'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_url' => ['nullable', 'url', 'max:500'],

            'status' => ['required', Rule::in([0, 1])],

            'created_by' => ['required', 'exists:users,id'],
        ];
    }

    /**
     * Update rules
     */
    protected function updateRules(): array
    {
        return [
            'name_en' => ['sometimes', 'required', 'string', 'max:255'],
            'name_bn' => ['sometimes', 'nullable', 'string', 'max:255'],

            'description_en' => ['sometimes', 'nullable', 'string'],
            'description_bn' => ['sometimes', 'nullable', 'string'],

            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_url' => ['sometimes', 'nullable', 'url', 'max:500'],

            'status' => ['sometimes', Rule::in([0, 1])],

            'updated_by' => ['required', 'exists:users,id'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'name_en.required' => 'English name is required.',
            'name_en.max' => 'English name cannot exceed 255 characters.',

            'image.image' => 'Uploaded file must be an image.',
            'image.mimes' => 'Image must be JPG, JPEG, PNG, or WEBP format.',
            'image.max' => 'Image size must not exceed 2MB.',

            'image_url.url' => 'Image URL must be a valid URL.',

            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either active (1) or inactive (0).',

            'created_by.required' => 'Creator information is required.',
            'created_by.exists' => 'Selected creator is invalid.',

            'updated_by.required' => 'Updater information is required.',
            'updated_by.exists' => 'Selected updater is invalid.',
        ];
    }
}