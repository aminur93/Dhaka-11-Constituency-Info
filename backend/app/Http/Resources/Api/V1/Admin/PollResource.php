<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PollResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title_en' => $this->title_en,
            'title_bn' => $this->title_bn,
            'description_en' => $this->description_en,
            'description_bn' => $this->description_bn,

            'poll_type' => $this->poll_type,

            'target_audience' => $this->target_audience,

            'ward_id' => $this->ward_id,
            'thana_id' => $this->thana_id,

            'is_anonymous' => (bool) $this->is_anonymous,
            'allow_multiple_votes' => (bool) $this->allow_multiple_votes,
            'status' => (bool) $this->status,

            'start_date' => optional($this->start_date)->toDateTimeString(),
            'end_date' => optional($this->end_date)->toDateTimeString(),

            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,

            'ward' => new WardResource($this->whenLoaded('ward')),
            'thana' => new ThanaResource($this->whenLoaded('thana')),
            'createdBy' => new UserResource($this->whenLoaded('createdBy')),
            'updatedBy' => new UserResource($this->whenLoaded('updatedBy')),
        ];
    }
}