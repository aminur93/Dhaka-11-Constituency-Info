<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnionResource extends JsonResource
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
            'name_en' => $this->name_en,
            'name_bn' => $this->name_bn,
            'code' => $this->code,
            'is_active' => $this->is_active,
            'thana' => new ThanaResource($this->whenLoaded('thana')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}