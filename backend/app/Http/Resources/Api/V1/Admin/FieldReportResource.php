<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FieldReportResource extends JsonResource
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

            // Relations
            'task_id' => new VolunteerTaskResource($this->whenLoaded('task')),
            'volunteer_id' => new volunteerResource($this->whenLoaded('volunteer')),

            // Report info
            'report_title' => $this->report_title,
            'report_description' => $this->report_description,
            'findings' => $this->findings,
            'recommendations' => $this->recommendations,
            'people_met' => $this->people_met,

            // Location
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,

            // Timing
            'submitted_at' => optional($this->submitted_at)->toDateTimeString(),

            // Future expansion (media)
            'media' => FieldReportMediaResource::collection($this->whenLoaded('media')),
        ];
    }
}