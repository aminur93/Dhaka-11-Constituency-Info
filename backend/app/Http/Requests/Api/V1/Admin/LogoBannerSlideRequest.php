<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LogoBannerSlideRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['logo', 'banner', 'slide'])],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    /**
     * Update rules
     */
    protected function updateRules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', Rule::in(['logo', 'banner', 'slide'])],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    /**
     * Custom messages
     */
    public function messages(): array
    {
        return [
            'type.in' => 'Type must be one of: logo, banner, slide.',
            'image.image' => 'Uploaded file must be an image.',
            'image.mimes' => 'Image must be a JPG or PNG file.',
            'image.max' => 'Image size must not exceed 2MB.',
        ];
    }
}