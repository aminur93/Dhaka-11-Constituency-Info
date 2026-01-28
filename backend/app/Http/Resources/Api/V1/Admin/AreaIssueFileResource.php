<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AreaIssueFileResource extends JsonResource
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
            'area_issue_id' => $this->area_issue_id,
            'file_path' => $this->file_path,
            'file_url' => $this->file_url,
            'file_type' => $this->file_type,
            'file_name' => $this->file_name,
            'file_size' => $this->file_size,
            'uploaded_by' => [
                'id' => $this->uploaded_by,
                'name' => $this->whenLoaded('uploader', fn () => $this->uploader->name ?? null),
            ],
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}