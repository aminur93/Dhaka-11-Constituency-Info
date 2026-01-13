<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VolunteerAreaAssignmentRequest extends FormRequest
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
        // Detect method type
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
            'volunteer_id' => ['required', 'integer', 'exists:volunteers,id'],
            'assigned_by' => ['nullable', 'integer', 'exists:users,id'],
            'ward_id' => ['nullable', 'integer', 'exists:wards,id'],
            'thana_id' => ['nullable', 'integer', 'exists:thanas,id'],
            'is_primary' => ['sometimes', 'boolean'],
            'assigned_at' => ['sometimes', 'date'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],

            // Composite unique: volunteer_id + ward_id
            'ward_id' => [
                'nullable',
                'integer',
                'exists:wards,id',
                Rule::unique('volunteer_area_assignments')
                    ->where(function ($query) {
                        return $query->where('volunteer_id', $this->volunteer_id);
                    }),
            ],
        ];
    }

    /**
     * Validation rules for UPDATE (PUT/PATCH)
     */
    protected function updateRules(): array
    {
        $assignmentId = $this->route('volunteer_area_assignment');

        return [
            'volunteer_id' => ['sometimes', 'integer', 'exists:volunteers,id'],
            'assigned_by' => ['nullable', 'integer', 'exists:users,id'],
            'ward_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:wards,id',
                Rule::unique('volunteer_area_assignments')
                    ->where(function ($query) {
                        return $query->where('volunteer_id', $this->volunteer_id);
                    })
                    ->ignore($assignmentId),
            ],
            'thana_id' => ['sometimes', 'nullable', 'integer', 'exists:thanas,id'],
            'is_primary' => ['sometimes', 'boolean'],
            'assigned_at' => ['sometimes', 'date'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * Optional: Custom messages
     */
    public function messages(): array
    {
        return [
            'volunteer_id.required' => 'Volunteer selection is required.',
            'volunteer_id.exists' => 'Selected volunteer does not exist.',
            'ward_id.unique' => 'This volunteer is already assigned to this ward.',
        ];
    }
}