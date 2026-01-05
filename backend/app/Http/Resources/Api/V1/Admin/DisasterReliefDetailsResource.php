<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DisasterReliefDetailsResource extends JsonResource
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

            'disaster_type' => $this->disaster_type,
            'disaster_date' => $this->disaster_date?->toDateString(),
            'loss_type' => $this->loss_type,
            'estimated_loss' => $this->estimated_loss,
            'family_affected' => $this->family_affected,
            'temporary_shelter_needed' => (bool)$this->temporary_shelter_needed,
            'relief_items_needed' => $this->relief_items_needed,

            'damage_photo' => [
                'file' => $this->damage_photo,
                'url'  => $this->damage_photo_url,
            ],
        ];
    }
}