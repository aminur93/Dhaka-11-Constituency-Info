<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DistrictRequest extends FormRequest
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
                'regex:/^[A-Z0-9_]+$/',
                'unique:districts,code',
            ],
            'division_id' => [
                'required',
                'integer',
                'exists:divisions,id',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    /**
     * Validation rules for update
     */
    protected function updateRules(): array
    {
        $districtId = $this->route('district');

        return [
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
                'max:50',
                'regex:/^[A-Z0-9_]+$/',
                Rule::unique('districts', 'code')->ignore($districtId),
            ],
            'division_id' => [
                'sometimes',
                'integer',
                'exists:divisions,id',
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
            'code.regex' => 'District code must be uppercase and may contain only letters, numbers, or underscores.',
            'code.unique' => 'This district code already exists.',
            'division_id.exists' => 'Selected division does not exist.',
        ];
    }
}