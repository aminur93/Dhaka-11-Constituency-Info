<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
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
     * Store rules (POST)
     */
    protected function storeRules(): array
    {
        return [
            'event_number' => ['nullable', 'string', 'max:50', 'unique:events,event_number'],

            'title_en' => ['required', 'string', 'max:500'],
            'title_bn' => ['nullable', 'string', 'max:500'],

            'description' => ['nullable', 'string'],

            'event_type' => [
                'required',
                Rule::in(['meeting', 'campaign', 'awareness', 'relief', 'cultural', 'sports']),
            ],

            'venue_en' => ['nullable', 'string', 'max:500'],
            'venue_bn' => ['nullable', 'string', 'max:500'],

            'ward_id' => ['nullable', 'exists:wards,id'],
            'thana_id' => ['nullable', 'exists:thanas,id'],

            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['nullable', 'date', 'after_or_equal:start_datetime'],

            'organizer_id' => ['required', 'exists:users,id'],

            'max_participants' => ['nullable', 'integer', 'min:1'],
            'registration_required' => ['nullable', 'boolean'],
            'registration_deadline' => ['nullable', 'date', 'before_or_equal:start_datetime'],

            'status' => [
                'nullable',
                Rule::in(['scheduled', 'ongoing', 'completed', 'cancelled']),
            ],

            'banner_image' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Update rules (PUT / PATCH)
     */
    protected function updateRules(): array
    {
        return [
            'event_number' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('events', 'event_number')->ignore($this->route('id')),
            ],

            'title_en' => ['sometimes', 'string', 'max:500'],
            'title_bn' => ['sometimes', 'nullable', 'string', 'max:500'],

            'description' => ['sometimes', 'nullable', 'string'],

            'event_type' => [
                'sometimes',
                Rule::in(['meeting', 'campaign', 'awareness', 'relief', 'cultural', 'sports']),
            ],

            'venue_en' => ['sometimes', 'nullable', 'string', 'max:500'],
            'venue_bn' => ['sometimes', 'nullable', 'string', 'max:500'],

            'ward_id' => ['sometimes', 'nullable', 'exists:wards,id'],
            'thana_id' => ['sometimes', 'nullable', 'exists:thanas,id'],

            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],

            'start_datetime' => ['sometimes', 'date'],
            'end_datetime' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_datetime'],

            'organizer_id' => ['sometimes', 'exists:users,id'],

            'max_participants' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'registration_required' => ['sometimes', 'boolean'],
            'registration_deadline' => ['sometimes', 'nullable', 'date', 'before_or_equal:start_datetime'],

            'status' => [
                'sometimes',
                Rule::in(['scheduled', 'ongoing', 'completed', 'cancelled']),
            ],

            'banner_image' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Event title is required.',
            'event_type.in' => 'Invalid event type selected.',
            'status.in' => 'Invalid event status.',
            'start_datetime.required' => 'Event start date & time is required.',
            'end_datetime.after_or_equal' => 'End date must be after start date.',
            'organizer_id.exists' => 'Invalid organizer selected.',
            'event_number.unique' => 'Event number already exists.',
        ];
    }
}