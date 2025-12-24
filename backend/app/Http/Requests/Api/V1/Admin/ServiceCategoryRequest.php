<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceCategoryRequest extends FormRequest
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
     * Store validation rules
     */
    protected function storeRules(): array
    {
        return [
            'name_en' => [
                'required',
                'string',
                'max:255',
            ],

            'name_bn' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description_en' => [
                'nullable',
                'string',
            ],

            'description_bn' => [
                'nullable',
                'string',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'status' => [
                'nullable',
                'in:0,1',
            ],
        ];
    }

    /**
     * Update validation rules
     */
    protected function updateRules(): array
    {
        return [
            'name_en' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'name_bn' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'description_en' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'description_bn' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'status' => [
                'sometimes',
                'in:0,1',
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'name_en.required' => 'English name is required.',
            'image.image' => 'Uploaded file must be a valid image.',
            'image.mimes' => 'Image must be jpg, jpeg, png or webp.',
            'image.max' => 'Image size must not exceed 2MB.',
            'status.in' => 'Status must be either active (1) or inactive (0).',
        ];
    }
}