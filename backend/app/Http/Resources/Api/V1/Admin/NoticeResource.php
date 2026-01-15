<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoticeResource extends JsonResource
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
            'ward_id' => new WardResource($this->whenLoaded('ward')),
            'thana_id' => new ThanaResource($this->whenLoaded('thana')),
            'title_en' => $this->title_en,
            'title_bn' => $this->title_bn,
            'content_en' => $this->content_en,
            'content_bn' => $this->content_bn,
            'category' => $this->category,
            'priority' => $this->priority,
            'target_audience' => $this->target_audience,
            'is_active' => $this->is_active,
            'attachment_file' => $this->attachment_file,
            'attachment_file_url' => $this->attachment_file_url,
            'published_at' => optional($this->published_at)->toDateTimeString(),
            'expires_at' => optional($this->expires_at)->toDateTimeString(),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}