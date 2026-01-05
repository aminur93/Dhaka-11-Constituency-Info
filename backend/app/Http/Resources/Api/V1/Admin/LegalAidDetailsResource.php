<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LegalAidDetailsResource extends JsonResource
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

            'case_type' => $this->case_type,
            'case_number' => $this->case_number,
            'court_name' => $this->court_name,
            'opponent_party' => $this->opponent_party,
            'case_description' => $this->case_description,

            'case_documents' => [
                'file' => $this->case_documents_file,
                'url'  => $this->case_documents_file_url,
            ],
        ];
    }
}