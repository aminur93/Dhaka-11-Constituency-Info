<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ThanaRequest extends FormRequest
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
            'district_id' => [
                'required',
                'integer',
                'exists:districts,id',
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
                'unique:thanas,code',
            ],

            'constituency' => [
                'nullable',
                'string',
                'max:150',
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
        $thanaId = $this->route('thana');

        return [
            'district_id' => [
                'sometimes',
                'integer',
                'exists:districts,id',
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
                'max:50',
                Rule::unique('thanas', 'code')->ignore($thanaId),
            ],

            'constituency' => [
                'sometimes',
                'string',
                'max:150',
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
            'code.unique' => 'This thana code already exists.',
            'district_id.exists' => 'Selected district does not exist.',
        ];
    }
}