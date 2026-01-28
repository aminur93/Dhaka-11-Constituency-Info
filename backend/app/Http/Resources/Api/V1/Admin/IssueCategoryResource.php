<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IssueCategoryResource extends JsonResource
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

            'name' => [
                'en' => $this->name_en,
                'bn' => $this->name_bn,
            ],

            'description' => [
                'en' => $this->description_en,
                'bn' => $this->description_bn,
            ],

            'image' => [
                'path' => $this->image,
                'url'  => $this->image_url ?? ($this->image ? asset('storage/' . $this->image) : null),
            ],

            'status' => (bool) $this->status,

            'createdBy' => new UserResource($this->whenLoaded('createdBy')),
            'updatedBy' => new UserResource($this->whenLoaded('updatedBy')),

        ];
    }
}