<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
             'event_number' => $this->event_number,

            // Titles
            'title_en' => $this->title_en,
            'title_bn' => $this->title_bn,

            // Description
            'description' => $this->description,

            // Type & Status
            'event_type' => $this->event_type,
            'status' => $this->status,

            // Venue
            'venue_en' => $this->venue_en,
            'venue_bn' => $this->venue_bn,

            // Location
            'ward_id' => $this->ward_id,
            'thana_id' => $this->thana_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,

            // Timing
            'start_datetime' => optional($this->start_datetime)->toDateTimeString(),
            'end_datetime' => optional($this->end_datetime)->toDateTimeString(),

            // Registration
            'registration_required' => (bool) $this->registration_required,
            'registration_deadline' => optional($this->registration_deadline)->toDateTimeString(),
            'max_participants' => $this->max_participants,

            // Media
            'banner_image' => $this->banner_image,
            'banner_image_url' => $this->banner_image_url,

            // Relations (when loaded)
            'organizer' => new UserResource($this->whenLoaded('organizer')),
            'ward' => new WardResource($this->whenLoaded('ward')),
            'thana' => new ThanaResource($this->whenLoaded('thana')),

            'createdBy' => new UserResource($this->whenLoaded('createdBy')),

        ];
    }
}