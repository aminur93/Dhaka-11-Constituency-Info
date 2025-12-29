<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DivisionRequest extends FormRequest
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
     * Validation rules for store request
     */
    protected function storeRules(): array
    {
        return [
            'name_en' => [
                'required',
                'string',
                'max:100',
            ],
            'name_bn' => [
                'required',
                'string',
                'max:100',
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z_]+$/',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    /**
     * Validation rules for update request
     */
    protected function updateRules(): array
    {
        // Route parameter name: division
        $divisionId = $this->route('division');

        return [
            'name_en' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'name_bn' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'code' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^[A-Z_]+$/',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'code.regex' => 'Division code must be uppercase and underscore separated (example: DHAKA_DIVISION).',
            'code.unique' => 'This division code already exists.',
            'is_active.boolean' => 'Is active must be true or false.',
        ];
    }
}