<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AreaIssueRequest extends FormRequest
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
     * Store Rules (Citizen reports issue)
     */
    protected function storeRules(): array
    {
        return [
            'issue_category_id' => ['required', 'exists:issue_categories,id'],
            'ward_id' => ['required', 'exists:wards,id'],
            'thana_id' => ['required', 'exists:thanas,id'],

            'reported_by' => ['nullable', 'exists:users,id'],

            'title_en' => ['required', 'string', 'max:500'],
            'title_bn' => ['required', 'string', 'max:500'],
            'description_en' => ['required', 'string'],
            'description_bn' => ['required', 'string'],

            'severity' => ['required', Rule::in(['low','medium','high','critical'])],

            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'source' => ['nullable', Rule::in(['app','web','admin','hotline'])],

            'is_anonymous' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Update Rules (Admin / Authority updates issue)
     */
    protected function updateRules(): array
    {
        return [
            'issue_category_id' => ['sometimes', 'exists:issue_categories,id'],
            'ward_id' => ['sometimes', 'exists:wards,id'],
            'thana_id' => ['sometimes', 'exists:thanas,id'],

            'title_en' => ['sometimes', 'string', 'max:500'],
            'title_bn' => ['sometimes', 'string', 'max:500'],
            'description_en' => ['sometimes', 'string'],
            'description_bn' => ['sometimes', 'string'],

            'severity' => ['sometimes', Rule::in(['low','medium','high','critical'])],

            'status' => ['sometimes', Rule::in([
                'reported',
                'verified',
                'assigned',
                'in_progress',
                'resolved',
                'closed',
                'rejected'
            ])],

            'assigned_to' => ['sometimes', 'nullable', 'exists:users,id'],

            'priority_score' => ['sometimes', 'integer', 'min:0', 'max:100'],

            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],

            'photo' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'resolved_at' => ['sometimes', 'nullable', 'date'],
        ];
    }

    /**
     * Custom Messages
     */
    public function messages(): array
    {
        return [
            'issue_category_id.required' => 'Issue category is required.',
            'issue_category_id.exists' => 'Selected issue category is invalid.',

            'ward_id.required' => 'Ward is required.',
            'thana_id.required' => 'Thana is required.',

            'title.required' => 'Issue title is required.',
            'description.required' => 'Issue description is required.',

            'severity.required' => 'Severity level is required.',
            'severity.in' => 'Invalid severity level selected.',

            'status.in' => 'Invalid status value.',

            'photo.image' => 'Uploaded file must be an image.',
            'photo.mimes' => 'Image must be jpg, jpeg, png, or webp.',
            'photo.max' => 'Image size must not exceed 4MB.',

            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
        ];
    }
}