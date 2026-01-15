<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NoticeRequest extends FormRequest
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
        $rules = [
            'title_en' => ['required', 'string', 'max:500'],
            'title_bn' => ['nullable', 'string', 'max:500'],
            'content_en' => ['required', 'string'],
            'content_bn' => ['nullable', 'string'],
            'category' => ['nullable', 'string', Rule::in(['announcement', 'alert', 'information', 'circular'])],
            'priority' => ['nullable', 'string', Rule::in(['low', 'normal', 'high'])],
            'target_audience' => ['nullable', 'string', Rule::in(['all', 'ward_specific', 'thana_specific'])],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'thana_id' => ['nullable', 'exists:thanas,id'],
            'is_active' => ['nullable', 'boolean'],
            'attachment_file' => ['nullable', 'string', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:published_at'],
        ];

        // For PUT/PATCH requests, all fields can be optional
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            foreach ($rules as $key => $rule) {
                $rules[$key] = array_merge(['sometimes'], is_array($rule) ? $rule : [$rule]);
            }
        }

        return $rules;
    }
}