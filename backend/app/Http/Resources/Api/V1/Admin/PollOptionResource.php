<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PollOptionResource extends JsonResource
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

            'poll_id' => new PollResource($this->whenLoaded('poll')),

            'option_text_en' => $this->option_text_en,
            'option_text_bn' => $this->option_text_bn,

            'display_order' => $this->display_order,
            'vote_count' => $this->vote_count,

            'status' => $this->status,

            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'updated_by' => new UserResource($this->whenLoaded('updatedBy')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}