<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FieldReportMediaResource extends JsonResource
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

            // Relation
            'report_id' => $this->report_id,

            // Media info
            'media_type' => $this->media_type,
            'file_path' => $this->file_path,
            'file_path_url' => $this->file_path_url,
            'caption' => $this->caption,

            // Location (optional)
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,

            // Timing
            'uploaded_at' => optional($this->uploaded_at)->toDateTimeString(),
        ];
    }
}