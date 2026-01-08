<?php

namespace App\Models;

use App\Helper\ImageUpload;
use Illuminate\Database\Eloquent\Model;

class MedicalAssistanceDetail extends Model
{
    protected $fillable = [
        'request_id',
        'patient_name',
        'patient_age',
        'patient_gender',
        'relation_with_applicant',
        'disease_type',
        'hospital_name',
        'doctor_name',
        'estimated_cost',
        'treatment_duration',
        'is_emergency',
        'prescription_file',
        'prescription_file_url',
        'medical_report_file',
        'medical_report_file_url',
    ];

    public function serviceApplicant()
    {
        return $this->belongsTo(ServiceApplicant::class, 'request_id');
    }

    protected static function booted()
    {
        static::deleting(function ($model) {
            if ($model->prescription_file) {
                ImageUpload::deleteApplicationStorage($model->prescription_file);
            }
            if ($model->medical_report_file) {
                ImageUpload::deleteApplicationStorage($model->medical_report_file);
            }
        });
    }
}