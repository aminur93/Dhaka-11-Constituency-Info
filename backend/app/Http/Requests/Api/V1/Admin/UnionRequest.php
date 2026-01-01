<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnionRequest extends FormRequest
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
     * Validation rules for store
     */
    protected function storeRules(): array
    {
        return [
            'thana_id' => [
                'required',
                'integer',
                'exists:thanas,id',
            ],

            'name_en' => [
                'required',
                'string',
                'max:150',
            ],

            'name_bn' => [
                'required',
                'string',
                'max:150',
            ],

            'code' => [
                'required',
                'string',
                'max:50',
                'unique:unions,code',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'created_by' => [
                'required',
                'integer',
                'exists:users,id',
            ],
        ];
    }

    /**
     * Validation rules for update
     */
    protected function updateRules(): array
    {
        $unionId = $this->route('union');

        return [
            'thana_id' => [
                'sometimes',
                'integer',
                'exists:thanas,id',
            ],

            'name_en' => [
                'sometimes',
                'string',
                'max:150',
            ],

            'name_bn' => [
                'sometimes',
                'string',
                'max:150',
            ],

            'code' => [
                'sometimes',
                'string',
                'max:50'
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
            'code.unique' => 'This union code already exists.',
            'thana_id.exists' => 'Selected thana does not exist.',
            'created_by.exists' => 'Invalid user reference.',
        ];
    }
}