<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
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
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH']);

        return [

            // Category
            'type' => [
                $isUpdate ? 'sometimes' : 'nullable',
                'string',
                'max:100',
            ],

            // Question (English)
            'question_en' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:1000',
            ],

            // Question (Bangla)
            'question_bn' => [
                'nullable',
                'string',
                'max:1000',
            ],

            // Answer (English)
            'answer_en' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
            ],

            // Answer (Bangla)
            'answer_bn' => [
                'nullable',
                'string',
            ],

            // Display order
            'display_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            // Active status
            'status' => [
                'nullable',
                'boolean',
            ],

            // View count (normally system controlled, but allowed if needed)
            'view_count' => [
                'nullable',
                'integer',
                'min:0',
            ],

            // Creator (admin side)
            'created_by' => [
                'nullable',
                'exists:users,id',
            ],
        ];
    }

    /**
     * Custom validation messages (optional but professional)
     */
    public function messages(): array
    {
        return [
            'question.required' => 'FAQ question (English) is required.',
            'answer.required' => 'FAQ answer (English) is required.',
            'created_by.exists' => 'Selected creator does not exist.',
        ];
    }
}