<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VolunteerTaskRequest extends FormRequest
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
            'task_number' => ['required', 'string', 'max:50', 'unique:volunteer_tasks,task_number'],
            'volunteer_id' => ['required', 'integer', 'exists:volunteers,id'],
            'assigned_by' => ['required', 'integer', 'exists:users,id'],
            'related_request_id' => ['nullable', 'integer', 'exists:service_applicants,id'],
            'task_type' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', 'string', 'max:20', Rule::in(['low','medium','high'])],
            'status' => ['nullable', 'string', 'max:30', Rule::in(['assigned','in_progress','completed','cancelled'])],
            'ward_id' => ['nullable', 'integer', 'exists:wards,id'],
            'location_details' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'deadline' => ['nullable', 'date'],
            'assigned_at' => ['nullable', 'date'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * Validation rules for UPDATE (PUT/PATCH)
     */
    protected function updateRules(): array
    {
        $taskId = $this->route('volunteer_task');

        return [
            'task_number' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('volunteer_tasks', 'task_number')->ignore($taskId),
            ],
            'volunteer_id' => ['sometimes', 'integer', 'exists:volunteers,id'],
            'assigned_by' => ['sometimes', 'integer', 'exists:users,id'],
            'related_request_id' => ['sometimes','nullable', 'integer', 'exists:service_applicants,id'],
            'task_type' => ['sometimes', 'nullable', 'string', 'max:100'],
            'title' => ['sometimes','string','max:500'],
            'description' => ['sometimes','nullable','string'],
            'priority' => ['sometimes', 'string', 'max:20', Rule::in(['low','medium','high'])],
            'status' => ['sometimes', 'string', 'max:30', Rule::in(['assigned','in_progress','completed','cancelled'])],
            'ward_id' => ['sometimes','nullable','integer','exists:wards,id'],
            'location_details' => ['sometimes','nullable','string'],
            'latitude' => ['sometimes','nullable','numeric','between:-90,90'],
            'longitude' => ['sometimes','nullable','numeric','between:-180,180'],
            'deadline' => ['sometimes','nullable','date'],
            'assigned_at' => ['sometimes','nullable','date'],
            'started_at' => ['sometimes','nullable','date'],
            'completed_at' => ['sometimes','nullable','date','after_or_equal:started_at'],
            'created_by' => ['sometimes','nullable','integer','exists:users,id'],
        ];
    }

    /**
     * Optional: Custom messages
     */
    public function messages(): array
    {
        return [
            'task_number.required' => 'Task number is required.',
            'task_number.unique' => 'Task number must be unique.',
            'title.required' => 'Task title is required.',
            'volunteer_id.required' => 'Volunteer selection is required.',
            'volunteer_id.exists' => 'Selected volunteer does not exist.',
            'assigned_by.required' => 'Assigned by user is required.',
            'assigned_by.exists' => 'Assigned by user does not exist.',
            'completed_at.after_or_equal' => 'Completed date must be after or equal to started date.',
        ];
    }
}