<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FieldReportRequest extends FormRequest
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
         // CREATE
        if ($this->isMethod('post')) {
            return $this->storeRules();
        }

        // UPDATE
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->updateRules();
        }

        return [];
    }

    protected function storeRules(): array
    {
        return [
            'task_id' => ['required', 'exists:volunteer_tasks,id'],
            'volunteer_id' => ['required', 'exists:volunteers,id'],

            'report_title' => ['nullable', 'string', 'max:500'],
            'report_description' => ['required', 'string'],
            'findings' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
            'people_met' => ['nullable', 'integer', 'min:0'],

            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'submitted_at' => ['nullable', 'date'],
        ];
    }

    protected function updateRules(): array
    {
        return [
            'task_id' => ['sometimes', 'exists:volunteer_tasks,id'],
            'volunteer_id' => ['sometimes', 'exists:volunteers,id'],

            'report_title' => ['sometimes', 'nullable', 'string', 'max:500'],
            'report_description' => ['sometimes', 'string'],
            'findings' => ['sometimes', 'nullable', 'string'],
            'recommendations' => ['sometimes', 'nullable', 'string'],
            'people_met' => ['sometimes', 'integer', 'min:0'],

            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],

            'submitted_at' => ['sometimes', 'date'],
        ];
    }
}