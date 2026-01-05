<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancialAidDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'aid_purpose' => $this->aid_purpose,
            'monthly_income' => $this->monthly_income,
            'family_members' => $this->family_members,
            'earning_members' => $this->earning_members,
            'current_debt' => $this->current_debt,
            'assets_description' => $this->assets_description,

            'income_proof' => [
                'file' => $this->income_proof_file,
                'url'  => $this->income_proof_file_url,
            ],
        ];
    }
}