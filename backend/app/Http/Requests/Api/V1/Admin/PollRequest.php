<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PollRequest extends FormRequest
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
     * Store (POST) rules
     */
    protected function storeRules(): array
    {
        return [
            'title_en' => ['required', 'string', 'max:500'],
            'title_bn' => ['nullable', 'string', 'max:500'],
            'description_en' => ['nullable', 'string'],
            'description_bn' => ['nullable', 'string'],

            'poll_type' => [
                'required',
                Rule::in(['opinion', 'feedback', 'survey', 'voting']),
            ],

            'target_audience' => [
                'required',
                Rule::in(['all', 'ward_specific', 'thana_specific']),
            ],

            'ward_id' => ['nullable', 'integer', 'exists:wards,id'],
            'thana_id' => ['nullable', 'integer', 'exists:thanas,id'],

            'is_anonymous' => ['boolean'],
            'allow_multiple_votes' => ['boolean'],
            'status' => ['boolean'],

            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    /**
     * Update (PUT / PATCH) rules
     */
    protected function updateRules(): array
    {
        return [
            'title_en' => ['sometimes', 'required', 'string', 'max:500'],
            'title_bn' => ['sometimes', 'nullable', 'string', 'max:500'],
            'description_en' => ['sometimes', 'nullable', 'string'],
            'description_bn' => ['sometimes', 'nullable', 'string'],

            'poll_type' => [
                'sometimes',
                Rule::in(['opinion', 'feedback', 'survey', 'voting']),
            ],

            'target_audience' => [
                'sometimes',
                Rule::in(['all', 'ward_specific', 'thana_specific']),
            ],

            'ward_id' => ['sometimes', 'nullable', 'integer', 'exists:wards,id'],
            'thana_id' => ['sometimes', 'nullable', 'integer', 'exists:thanas,id'],

            'is_anonymous' => ['sometimes', 'boolean'],
            'allow_multiple_votes' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'boolean'],

            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Poll title is required.',
            'title.max' => 'Poll title cannot exceed 500 characters.',

            'poll_type.in' => 'Invalid poll type selected.',
            'target_audience.in' => 'Invalid target audience.',

            'ward_id.exists' => 'Selected ward does not exist.',
            'thana_id.exists' => 'Selected thana does not exist.',

            'end_date.after_or_equal' => 'End date must be equal to or after start date.',
        ];
    }
}