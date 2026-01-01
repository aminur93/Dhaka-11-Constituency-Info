<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WardRequest extends FormRequest
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

            'union_id' => [
                'nullable',
                'integer',
                'exists:unions,id',
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

            'ward_number' => [
                'required',
                'integer',
                'min:1',
            ],

            'area_type' => [
                'required',
                'string',
                'in:urban,rural',
            ],

            'population_estimate' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'total_households' => [
                'nullable',
                'integer',
                'min:0',
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
        $wardId = $this->route('ward');

        return [
            'thana_id' => [
                'sometimes',
                'integer',
                'exists:thanas,id',
            ],

            'union_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:unions,id',
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

            'ward_number' => [
                'sometimes',
                'integer',
                'min:1',
            ],

            'area_type' => [
                'sometimes',
                'string',
                'in:urban,rural',
            ],

            'population_estimate' => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
            ],

            'total_households' => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
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
            'thana_id.exists' => 'Selected thana does not exist.',
            'union_id.exists' => 'Selected union does not exist.',
            'ward_number.min' => 'Ward number must be greater than zero.',
            'area_type.in' => 'Area type must be city, rural, or municipal.',
        ];
    }
}