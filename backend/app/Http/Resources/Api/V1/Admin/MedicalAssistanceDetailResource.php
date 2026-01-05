<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalAssistanceDetailResource extends JsonResource
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

            'patient_name' => $this->patient_name,
            'patient_age' => $this->patient_age,
            'patient_gender' => $this->patient_gender,
            'relation_with_applicant' => $this->relation_with_applicant,

            'disease_type' => $this->disease_type,
            'hospital_name' => $this->hospital_name,
            'doctor_name' => $this->doctor_name,

            'estimated_cost' => $this->estimated_cost,
            'treatment_duration' => $this->treatment_duration,
            'is_emergency' => (bool) $this->is_emergency,

            'prescription' => [
                'file' => $this->prescription_file,
                'url'  => $this->prescription_file_url,
            ],

            'medical_report' => [
                'file' => $this->medical_report_file,
                'url'  => $this->medical_report_file_url,
            ],
        ];
    }
}