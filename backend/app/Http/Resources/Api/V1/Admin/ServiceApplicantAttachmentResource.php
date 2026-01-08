<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceApplicantAttachmentResource extends JsonResource
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

            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'file_url'  => $this->file_path
                ? asset('storage/' . $this->file_path)
                : null,

            'file_type' => $this->file_type,
            'file_size' => $this->file_size,

            'uploaded_by' => $this->uploaded_by,
        ];
    }
}