<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobSkillRegistrationResource extends JsonResource
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

            'qualification' => $this->qualification,
            'experience_years' => $this->experience_years,
            'skills' => $this->skills,
            'preferred_sector' => $this->preferred_sector,
            'training_interest' => $this->training_interest,
            'employment_status' => $this->employment_status,

            'cv' => [
                'file' => $this->cv_file,
                'url'  => $this->cv_file_url,
            ],
        ];
    }
}