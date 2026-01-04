<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WardCommissionerRequest extends FormRequest
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
     * =========================
     * Store (POST) Rules
     * =========================
     */
    protected function storeRules(): array
    {
        return [

            /* ---------- Main Table ---------- */

            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'ward_id' => [
                'required',
                'integer',
                'exists:wards,id',
            ],
            'commissioner_id' => [
                'required',
                'string',
                'max:50',
                'unique:ward_commissioners,commissioner_id',
            ],
            'full_name_en' => [
                'required',
                'string',
                'max:200',
            ],
            'full_name_bn' => [
                'nullable',
                'string',
                'max:200',
            ],
            'phone_en' => [
                'required',
                'string',
                'max:15',
            ],
            'phone_bn' => [
                'nullable',
                'string',
                'max:15',
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
            ],
            'nid_number_en' => [
                'nullable',
                'string',
                'max:17',
            ],
            'nid_number_bn' => [
                'nullable',
                'string',
                'max:17',
            ],
            'photo' => [
                'nullable',
                'string',
                'max:500',
            ],
            'political_party' => [
                'nullable',
                'string',
                'max:100',
            ],
            'term_start_date' => [
                'required',
                'date',
            ],
            'term_end_date' => [
                'nullable',
                'date',
                'after_or_equal:term_start_date',
            ],
            'election_year' => [
                'nullable',
                'integer',
                'digits:4',
            ],
            'status' => [
                'required',
                Rule::in([1,0,2,3]),
            ],
            'is_current' => [
                'required',
                'boolean',
            ],
            'created_by' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            /* ---------- Details Table ---------- */

            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
            ],
            'gender' => [
                'nullable',
                Rule::in(['male', 'female', 'other']),
            ],
            'blood_group' => [
                'nullable',
                'string',
                'max:5',
            ],
            'education' => [
                'nullable',
                'string',
                'max:500',
            ],
            'profession' => [
                'nullable',
                'string',
                'max:200',
            ],
            'previous_experience' => [
                'nullable',
                'string',
            ],
            'achievements' => [
                'nullable',
                'string',
            ],
            'social_activities' => [
                'nullable',
                'string',
            ],
            'emergency_contact' => [
                'nullable',
                'string',
                'max:15',
            ],
            'permanent_address_en' => [
                'nullable',
                'string',
            ],
            'permanent_address_bn' => [
                'nullable',
                'string',
            ],
            'present_address_en' => [
                'nullable',
                'string',
            ],
            'present_address_bn' => [
                'nullable',
                'string',
            ],
            'facebook_url' => [
                'nullable',
                'url',
                'max:500',
            ],
            'twitter_url' => [
                'nullable',
                'url',
                'max:500',
            ],
            'office_address_en' => [
                'nullable',
                'string',
                'max:500',
            ],
            'office_address_bn' => [
                'nullable',
                'string',
                'max:500',
            ],
            'office_phone_en' => [
                'nullable',
                'string',
                'max:15',
            ],
            'office_phone_bn' => [
                'nullable',
                'string',
                'max:15',
            ],
            'office_hours' => [
                'nullable',
                'string',
                'max:200',
            ],
            'public_meeting_schedule' => [
                'nullable',
                'string',
                'max:500',
            ],
            'biography_en' => [
                'nullable',
                'string',
            ],
            'biography_bn' => [
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * =========================
     * Update (PUT/PATCH) Rules
     * =========================
     */
    protected function updateRules(): array
    {
        $commissionerId = $this->route('ward_commissioner');

        return [

            'user_id' => [
                'sometimes',
                'integer',
                'exists:users,id',
            ],
            'ward_id' => [
                'sometimes',
                'integer',
                'exists:wards,id',
            ],
            'commissioner_id' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('ward_commissioners', 'commissioner_id')
                    ->ignore($commissionerId),
            ],
            'full_name_en' => [
                'sometimes',
                'string',
                'max:200',
            ],
            'full_name_bn' => [
                'sometimes',
                'string',
                'max:200',
            ],
            'phone_en' => [
                'sometimes',
                'string',
                'max:15',
            ],
            'phone_bn' => [
                'sometimes',
                'string',
                'max:15',
            ],
            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:100',
            ],
            'nid_number_en' => [
                'sometimes',
                'nullable',
                'string',
                'max:17',
            ],
            'nid_number_bn' => [
                'sometimes',
                'nullable',
                'string',
                'max:17',
            ],
            'photo' => [
                'sometimes',
                'nullable',
                'string',
                'max:500',
            ],
            'political_party' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],
            'term_start_date' => [
                'sometimes',
                'date',
            ],
            'term_end_date' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:term_start_date',
            ],
            'election_year' => [
                'sometimes',
                'nullable',
                'integer',
                'digits:4',
            ],
            'status' => [
                'sometimes',
                Rule::in([1,0,2,3]),
            ],
            'is_current' => [
                'sometimes',
                'boolean',
            ],

            // Details (same as store but sometimes)
            'date_of_birth' => ['sometimes', 'nullable', 'date', 'before:today'],
            'gender' => ['sometimes', 'nullable', Rule::in(['male', 'female', 'other'])],
            'blood_group' => ['sometimes', 'nullable', 'string', 'max:5'],
            'education' => ['sometimes', 'nullable', 'string', 'max:500'],
            'profession' => ['sometimes', 'nullable', 'string', 'max:200'],
            'previous_experience' => ['sometimes', 'nullable', 'string'],
            'achievements' => ['sometimes', 'nullable', 'string'],
            'social_activities' => ['sometimes', 'nullable', 'string'],
            'emergency_contact' => ['sometimes', 'nullable', 'string', 'max:15'],
            'permanent_address_en' => ['sometimes', 'nullable', 'string'],
            'permanent_address_bn' => ['sometimes', 'nullable', 'string'],
            'present_address_en' => ['sometimes', 'nullable', 'string'],
            'present_address_bn' => ['sometimes', 'nullable', 'string'],
            'facebook_url' => ['sometimes', 'nullable', 'url', 'max:500'],
            'twitter_url' => ['sometimes', 'nullable', 'url', 'max:500'],
            'office_address_en' => ['sometimes', 'nullable', 'string', 'max:500'],
            'office_address_bn' => ['sometimes', 'nullable', 'string', 'max:500'],
            'office_phone_en' => ['sometimes', 'nullable', 'string', 'max:15'],
            'office_phone_bn' => ['sometimes', 'nullable', 'string', 'max:15'],
            'office_hours' => ['sometimes', 'nullable', 'string', 'max:200'],
            'public_meeting_schedule' => ['sometimes', 'nullable', 'string', 'max:500'],
            'biography_en' => ['sometimes', 'nullable', 'string'],
            'biography_bn' => ['sometimes', 'nullable', 'string'],
        ];
    }
}