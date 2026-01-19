<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventRegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'event_id' => $this->event_id,
            'user_id'  => $this->user_id,

            'attendance_status' => $this->attendance_status,
            'registered_at'     => optional($this->registered_at)->toDateTimeString(),

            // Relationships (optional, when loaded)
            'event' => $this->whenLoaded('event', function () {
                return [
                    'id'    => $this->event->id,
                    'title_en' => $this->event->title_en,
                    'title_bn' => $this->event->title_bn,
                ];
            }),

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'    => $this->user->id,
                    'name'  => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}