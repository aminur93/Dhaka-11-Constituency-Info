<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
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

            'type' => $this->type,

            'question_en' => $this->question_en,
            'question_bn' => $this->question_bn,

            'answer_en' => $this->answer_en,
            'answer_bn' => $this->answer_bn,

            'display_order' => $this->display_order,

            'status' => (bool) $this->status,
            'view_count' => (int) $this->view_count,

            'created_by' => $this->created_by,

            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                    'email' => $this->creator->email,
                ];
            }),
        ];
    }
}