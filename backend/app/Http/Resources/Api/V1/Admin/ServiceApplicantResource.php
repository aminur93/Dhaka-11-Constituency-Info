<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceApplicantResource extends JsonResource
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
            'request_number' => $this->request_number,

            'subject' => $this->subject,
            'description' => $this->description,

            'priority' => $this->priority,
            'status' => $this->status,

            'requested_amount' => $this->requested_amount,
            'approved_amount' => $this->approved_amount,

            'category' => new ServiceCategoryResource($this->whenLoaded('serviceCategories')),

            'ward' => new WardResource($this->whenLoaded('ward')),
            'union' => new UnionResource($this->whenLoaded('union')),
            'thana' => new ThanaResource($this->whenLoaded('thana')),
            'district' => new DistrictResource($this->whenLoaded('district')),
            'division' => new DivisionResource($this->whenLoaded('division')),

            'remarks' => $this->remarks,
            'rejection_reason' => $this->rejection_reason,
            'completion_notes' => $this->completion_notes,

            'dates' => [
                'submitted_at' => $this->submitted_at?->toDateTimeString(),
                'reviewed_at' => $this->reviewed_at?->toDateTimeString(),
                'approved_at' => $this->approved_at?->toDateTimeString(),
                'completed_at' => $this->completed_at?->toDateTimeString(),
            ],

            'attachments' => ServiceApplicantAttachmentResource::collection(
                $this->whenLoaded('attachments')
            ),

            'statusHistory' => ServiceApplicantStatusResource::collection(
                $this->whenLoaded('statuses')
            ),

            'medicalDetails' => MedicalAssistanceDetailResource::make($this->whenLoaded('medicalDetails')),
            'financialDetails' => FinancialAidDetailsResource::make($this->whenLoaded('financialDetails')),
            'educationDetails' => EducationSupportDetailsResource::make($this->whenLoaded('educationDetails')),
            'jobDetails' => JobSkillRegistrationResource::make($this->whenLoaded('jobDetails')),
            'legalAidDetails' => LegalAidDetailsResource::make($this->whenLoaded('legalAidDetails')),
            'disasterDetails' => DisasterReliefDetailsResource::make($this->whenLoaded('disasterDetails')),

        ];
    }
}