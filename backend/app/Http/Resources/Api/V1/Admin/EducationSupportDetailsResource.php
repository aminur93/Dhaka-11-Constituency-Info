<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EducationSupportDetailsResource extends JsonResource
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

            'student_name' => $this->student_name,
            'student_age' => $this->student_age,
            'education_level' => $this->education_level,
            'institution_name' => $this->institution_name,
            'class_year' => $this->class_year,
            'gpa_cgpa' => $this->gpa_cgpa,
            'support_type' => $this->support_type,

            'academic_certificate' => [
                'file' => $this->academic_certificate_file,
                'url'  => $this->academic_certificate_file_url,
            ],
        ];
    }
}