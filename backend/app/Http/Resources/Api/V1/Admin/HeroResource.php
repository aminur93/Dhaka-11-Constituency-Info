<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeroResource extends JsonResource
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
            'title_en' => $this->title_en,
            'title_bn' => $this->title_bn,
            'sub_title_en' => $this->sub_title_en,
            'sub_title_bn' => $this->sub_title_bn,
            'description_en' => $this->description_en,
            'description_bn' => $this->description_bn,
            'image' => $this->image_url,
            'status' => $this->status
        ];
    }
}