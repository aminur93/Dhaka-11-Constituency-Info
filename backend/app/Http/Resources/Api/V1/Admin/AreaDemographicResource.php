<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AreaDemographicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ward_id' => $this->ward_id,
            'ward' => new WardResource($this->whenLoaded('ward')),
            'thana_id' => $this->thana_id,
            'thana' => new ThanaResource($this->whenLoaded('thana')),
            'total_population' => $this->total_population,
            'male_population' => $this->male_population,
            'female_population' => $this->female_population,
            'age_0_18' => $this->age_0_18,
            'age_19_35' => $this->age_19_35,
            'age_36_60' => $this->age_36_60,
            'age_above_60' => $this->age_above_60,
            'total_voters' => $this->total_voters,
            'literacy_rate' => $this->literacy_rate,
            'avg_income' => $this->avg_income,
            'updated_year' => $this->updated_year,
            'created_by' => $this->created_by,
            'user' => new UserResource($this->whenLoaded('user')),

        ];
    }
}