<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCategoryResource extends JsonResource
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
            'description_en' => $this->description_en,
            'description_bn' => $this->description_bn,
            'image' => $this->image,
            'image_url' => $this->image_url,
            'status' => $this->status
        ];
    }
}