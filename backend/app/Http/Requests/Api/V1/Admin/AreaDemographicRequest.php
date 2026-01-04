<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AreaDemographicRequest extends FormRequest
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
            'ward_id' => [
                'required',
                'integer',
                'exists:wards,id',
            ],
            'thana_id' => [
                'required',
                'integer',
                'exists:thanas,id',
            ],
            'total_population' => [
                'required',
                'integer',
                'min:0',
            ],
            'male_population' => [
                'required',
                'integer',
                'min:0',
            ],
            'female_population' => [
                'required',
                'integer',
                'min:0',
            ],
            'age_0_18' => [
                'required',
                'integer',
                'min:0',
            ],
            'age_19_35' => [
                'required',
                'integer',
                'min:0',
            ],
            'age_36_60' => [
                'required',
                'integer',
                'min:0',
            ],
            'age_above_60' => [
                'required',
                'integer',
                'min:0',
            ],
            'total_voters' => [
                'required',
                'integer',
                'min:0',
            ],
            'literacy_rate' => [
                'required',
                'numeric',
                'between:0,100',
            ],
            'avg_income' => [
                'required',
                'numeric',
                'min:0',
            ],
            'updated_year' => [
                'required',
                'integer',
                'digits:4',
                'min:1900',
                'max:' . date('Y'),
            ],
            'created_by' => [
                'nullable',
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
        return [
            'ward_id' => [
                'sometimes',
                'integer',
                'exists:wards,id',
            ],
            'thana_id' => [
                'sometimes',
                'integer',
                'exists:thanas,id',
            ],
            'total_population' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'male_population' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'female_population' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'age_0_18' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'age_19_35' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'age_36_60' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'age_above_60' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'total_voters' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'literacy_rate' => [
                'sometimes',
                'numeric',
                'between:0,100',
            ],
            'avg_income' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
            'updated_year' => [
                'sometimes',
                'integer',
                'digits:4',
                'min:1900',
                'max:' . date('Y'),
            ],
            'created_by' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'ward_id.exists' => 'Selected ward does not exist.',
            'thana_id.exists' => 'Selected thana does not exist.',
            'literacy_rate.between' => 'Literacy rate must be between 0 and 100.',
            'updated_year.max' => 'Updated year cannot be in the future.',
        ];
    }
}