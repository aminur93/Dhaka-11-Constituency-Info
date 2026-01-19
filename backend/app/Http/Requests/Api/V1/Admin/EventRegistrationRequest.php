<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EventRegistrationRequest extends FormRequest
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
            'event_id' => [
                'required',
                'integer',
                'exists:events,id',
                Rule::unique('event_registrations')
                    ->where(fn ($q) => $q->where('user_id', $this->user_id ?? Auth::id())),
            ],

            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'attendance_status' => [
                'nullable',
                Rule::in(['registered', 'attended', 'absent']),
            ],
        ];
    }

    /**
     * Update (PUT / PATCH) rules
     */
    protected function updateRules(): array
    {
        return [
            'attendance_status' => [
                'sometimes',
                Rule::in(['registered', 'attended', 'absent']),
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'event_id.required' => 'Event is required.',
            'event_id.exists' => 'Selected event does not exist.',
            'event_id.unique' => 'You are already registered for this event.',

            'user_id.exists' => 'Selected user does not exist.',

            'attendance_status.in' =>
                'Attendance status must be registered, attended, or absent.',
        ];
    }
}