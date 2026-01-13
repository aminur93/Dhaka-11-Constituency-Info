<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VolunteerTaskResource extends JsonResource
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
            'task_number' => $this->task_number,
            'volunteer_id' => new volunteerResource($this->whenLoaded('volunteer')),
            'assigned_by' => new UserResource($this->whenLoaded('assignedBy')),
            'related_request_id' => new ServiceApplicantResource($this->whenLoaded('serviceApplication')),
            'task_type' => $this->task_type,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'ward_id' => new WardResource($this->whenLoaded('ward')),
            'location_details' => $this->location_details,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'deadline' => $this->deadline,
            'assigned_at' => $this->assigned_at,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}