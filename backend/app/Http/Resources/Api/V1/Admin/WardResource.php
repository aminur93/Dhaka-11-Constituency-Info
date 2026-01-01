<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WardResource extends JsonResource
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
            'thana_id' => $this->thana_id,
            'thana' => new ThanaResource($this->whenLoaded('thana')),
            'union_id' => $this->union_id,
            'union' => new UnionResource($this->whenLoaded('union')),
            'name_en' => $this->name_en,
            'name_bn' => $this->name_bn,
            'ward_number' => $this->ward_number,
            'area_type' => $this->area_type,
            'population_estimate' => $this->population_estimate,
            'total_households' => $this->total_households,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'created_by_user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}