<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VolunteerAreaAssignmentResource extends JsonResource
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
            'volunteer_id' => new volunteerResource($this->whenLoaded('volunteer')),
            'assigned_by' => new UserResource($this->whenLoaded('assignedBy')),
            'ward_id' => new WardResource($this->whenLoaded('ward')),
            'thana_id' => $this->thana_id,
            'is_primary' => $this->is_primary,
            'assigned_at' => $this->assigned_at,
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}