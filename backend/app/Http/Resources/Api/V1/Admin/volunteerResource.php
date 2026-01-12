<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class volunteerResource extends JsonResource
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
            'user_id' => new UserResource($this->whenLoaded('user')),
            'volunteer_id' => $this->volunteer_id,
            'designation' => $this->designation,
            'specialization' => $this->specialization,
            'education' => $this->education,
            'profession' => $this->profession,
            'blood_group' => $this->blood_group,
            'emergency_contact' => $this->emergency_contact,
            'availability' => $this->availability,
            'skills' => $this->skills,
            'languages_spoken' => $this->languages_spoken,
            'volunteer_since' => $this->volunteer_since,
            'status' => $this->status,
            'rating' => $this->rating,
            'total_tasks_completed' => $this->total_tasks_completed,
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}