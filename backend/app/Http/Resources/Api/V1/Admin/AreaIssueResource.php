<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AreaIssueResource extends JsonResource
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
            'issue_code' => $this->issue_code,

            'category' => [
                'id' => $this->issue_category_id,
                'name_en' => $this->whenLoaded('category', fn () => $this->category->name_en),
                'name_bn' => $this->whenLoaded('category', fn () => $this->category->name_bn),
            ],

            'location' => [
                'ward_id' => $this->ward_id,
                'ward_name' => $this->whenLoaded('ward', fn () => $this->ward->name_en ?? null),
                'thana_id' => $this->thana_id,
                'thana_name' => $this->whenLoaded('thana', fn () => $this->thana->name_en ?? null),
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],

            'reporter' => [
                'id' => $this->reported_by,
                'name' => $this->whenLoaded('reporter', fn () => $this->reporter->name ?? null),
            ],

            'title_en' => $this->title_en,
            'title_bn' => $this->title_bn,
            'description_en' => $this->description_en,
            'description_bn' => $this->description_bn,

            'severity' => $this->severity,
            'status' => $this->status,
            'priority_score' => $this->priority_score,

            'source' => $this->source,
            'is_anonymous' => (bool) $this->is_anonymous,

            'photo' => $this->photo,
            'photo_url' => $this->photo_url,

            'assigned_to' => [
                'id' => $this->assigned_to,
                'name' => $this->whenLoaded('assignee', fn () => $this->assignee->name ?? null),
            ],

            'timeline' => [
                'reported_at' => $this->reported_at?->toDateTimeString(),
                'resolved_at' => $this->resolved_at?->toDateTimeString(),
                'last_updated' => $this->updated_at?->toDateTimeString(),
            ],
            
            'files' => $this->whenLoaded('areaIssueFile', fn () => AreaIssueFileResource::collection($this->areaIssueFile)),
        ];
    }
}