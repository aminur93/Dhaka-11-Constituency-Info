<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WardCommissionerResource extends JsonResource
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
            'user_id' => $this->user_id,
            'ward_id' => $this->ward_id,
            'commissioner_id' => $this->commissioner_id,
            'full_name_en' => $this->full_name_en,
            'full_name_bn' => $this->full_name_bn,
            'phone_en' => $this->phone_en,
            'phone_bn' => $this->phone_bn,
            'email' => $this->email,
            'nid_number_en' => $this->nid_number_en,
            'nid_number_bn' => $this->nid_number_bn,
            'photo' => $this->photo,
            'political_party' => $this->political_party,
            'term_start_date' => $this->term_start_date,
            'term_end_date' => $this->term_end_date,
            'election_year' => $this->election_year,
            'status' => $this->status,
            'is_current' => $this->is_current,
            'created_by' => $this->created_by,
            'details' => new WardCommissionerDetailsResource($this->whenLoaded('details')),
            'ward' => new WardResource($this->whenLoaded('ward')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_by_user' => new UserResource($this->whenLoaded('createdByUser')),
        ];
    }
}