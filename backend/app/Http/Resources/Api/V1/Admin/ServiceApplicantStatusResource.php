<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceApplicantStatusResource extends JsonResource
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

            'request_id' => $this->request_id,

            'old_status' => $this->old_status,
            'new_status' => $this->new_status,

            'remarks' => $this->remarks,

            'changed_by' => $this->changed_by,

            'changed_at' => $this->changed_at?->toDateTimeString(),
        ];
    }
}