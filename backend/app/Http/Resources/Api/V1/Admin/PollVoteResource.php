<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PollVoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'poll_id'    => new PollResource($this->whenLoaded('poll')),
            'option_id'  => new PollOptionResource($this->whenLoaded('option')),
            'user_id'    => new UserResource($this->whenLoaded('user')),
            'voted_at'   => $this->voted_at,
            'created_at' => $this->created_at,
        ];
    }
}