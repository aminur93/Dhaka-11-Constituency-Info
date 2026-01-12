<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class volunteerRequest extends FormRequest
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
     * Validation rules for STORE (POST)
     */
    protected function storeRules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'volunteer_id' => ['required', 'string', 'max:50', 'unique:volunteers,volunteer_id'],
            'designation' => ['nullable', 'string', 'max:100'],
            'specialization' => ['nullable', 'string', 'max:200'],
            'education' => ['nullable', 'string', 'max:200'],
            'profession' => ['nullable', 'string', 'max:200'],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'emergency_contact' => ['nullable', 'string', 'max:15'],
            'availability' => ['nullable', 'string', 'max:500'],
            'skills' => ['nullable', 'string'],
            'languages_spoken' => ['nullable', 'string', 'max:200'],
            'volunteer_since' => ['nullable', 'date'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive', 'suspended'])],
            'rating' => ['nullable', 'numeric', 'between:0,5'],
            'total_tasks_completed' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Validation rules for UPDATE (PUT/PATCH)
     */
    protected function updateRules(): array
    {
        $volunteerId = $this->route('volunteer'); // assuming route parameter is {volunteer}

        return [
            'user_id' => ['sometimes', 'exists:users,id'],
            'volunteer_id' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('volunteers', 'volunteer_id')->ignore($volunteerId),
            ],
            'designation' => ['nullable', 'string', 'max:100'],
            'specialization' => ['nullable', 'string', 'max:200'],
            'education' => ['nullable', 'string', 'max:200'],
            'profession' => ['nullable', 'string', 'max:200'],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'emergency_contact' => ['nullable', 'string', 'max:15'],
            'availability' => ['nullable', 'string', 'max:500'],
            'skills' => ['nullable', 'string'],
            'languages_spoken' => ['nullable', 'string', 'max:200'],
            'volunteer_since' => ['nullable', 'date'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive', 'suspended'])],
            'rating' => ['nullable', 'numeric', 'between:0,5'],
            'total_tasks_completed' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'volunteer_id.unique' => 'This volunteer ID is already taken.',
            'status.in' => 'Status must be active, inactive, or suspended.',
            'rating.between' => 'Rating must be between 0 and 5.',
        ];
    }
}