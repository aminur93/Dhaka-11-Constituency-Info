<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceApplicantRequest extends FormRequest
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
            'request_number' => [
                'required',
                'string',
                'max:50',
            ],

            'user_id' => [
                'required',
                'exists:users,id',
            ],

            'service_category_id' => [
                'required',
                'exists:service_categories,id',
            ],

            'ward_id' => [
                'nullable',
                'exists:wards,id',
            ],

            'priority' => [
                'nullable',
                'in:low,medium,high,urgent',
            ],

            'status' => [
                'nullable',
                'in:pending,in_review,approved,in_progress,completed,rejected,cancelled',
            ],

            'subject' => [
                'required',
                'string',
                'max:500',
            ],

            'description' => [
                'required',
                'string',
            ],

            'requested_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'approved_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'lte:requested_amount',
            ],

            'assigned_to' => [
                'nullable',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],

            'rejection_reason' => [
                'nullable',
                'string',
            ],

            'completion_notes' => [
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * Update validation rules
     */
    protected function updateRules(): array
    {
        return [
            'request_number' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'category_id' => [
                'sometimes',
                'exists:service_categories,id',
            ],

            'ward_id' => [
                'sometimes',
                'nullable',
                'exists:wards,id',
            ],

            'priority' => [
                'sometimes',
                'in:low,medium,high,urgent',
            ],

            'status' => [
                'sometimes',
                'in:pending,in_review,approved,in_progress,completed,rejected,cancelled',
            ],

            'subject' => [
                'sometimes',
                'string',
                'max:500',
            ],

            'description' => [
                'sometimes',
                'string',
            ],

            'requested_amount' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
            ],

            'approved_amount' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
                'lte:requested_amount',
            ],

            'assigned_to' => [
                'sometimes',
                'nullable',
                'exists:users,id',
            ],

            'remarks' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'rejection_reason' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'completion_notes' => [
                'sometimes',
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'request_number.required' => 'Request number is required.',
            'request_number.unique'   => 'Request number must be unique.',

            'user_id.required' => 'Applicant user is required.',
            'user_id.exists'   => 'Selected user does not exist.',

            'category_id.required' => 'Service category is required.',
            'category_id.exists'   => 'Invalid service category.',

            'priority.in' => 'Priority must be low, medium, high or urgent.',

            'status.in' => 'Invalid request status.',

            'subject.required' => 'Subject is required.',
            'subject.max'      => 'Subject may not exceed 500 characters.',

            'requested_amount.numeric' => 'Requested amount must be numeric.',
            'approved_amount.lte'      => 'Approved amount cannot exceed requested amount.',
        ];
    }
}