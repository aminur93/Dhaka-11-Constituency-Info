<?php

namespace App\Models;

use App\Helper\ImageUpload;
use Illuminate\Database\Eloquent\Model;

class EducationSupportDetails extends Model
{
    protected $fillable = [
        'request_id',
        'student_name',
        'student_age',
        'education_level',
        'institution_name',
        'class_year',
        'gpa_cgpa',
        'support_type',
        'academic_certificate_file',
        'academic_certificate_file_url',
    ];

    public function serviceApplicant()
    {
        return $this->belongsTo(ServiceApplicant::class, 'request_id');
    }

    protected static function booted()
    {
        static::deleting(function ($model) {
            if ($model->academic_certificate_file) {
                ImageUpload::deleteApplicationStorage($model->academic_certificate_file);
            }
        });
    }
}