<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PollOptionRequest extends FormRequest
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

    protected function storeRules(): array
    {
        return [
            'poll_id' => ['required', 'exists:polls,id'],
            'option_text_en' => ['required', 'string', 'max:500'],
            'option_text_bn' => ['nullable', 'string', 'max:500'],
            'display_order' => ['nullable', 'integer', 'min:1'],
            'status' => ['boolean'],
        ];
    }

    protected function updateRules(): array
    {
        return [
            'poll_id' => ['sometimes', 'exists:polls,id'],
            'option_text_en' => ['sometimes', 'string', 'max:500'], // fixed name
            'option_text_bn' => ['nullable', 'string', 'max:500'],
            'display_order' => ['nullable', 'integer', 'min:1'],
            'vote_count' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'boolean'],
        ];
    }

}