<?php

namespace App\Models;

use App\Helper\ImageUpload;
use Illuminate\Database\Eloquent\Model;

class JobSkillRegistration extends Model
{
    protected $fillable = [
        'request_id',
        'qualification',
        'experience_years',
        'skills',
        'preferred_sector',
        'training_interest',
        'employment_status',
        'cv_file',
        'cv_file_url',
    ];

    public function serviceApplicant()
    {
        return $this->belongsTo(ServiceApplicant::class, 'request_id');
    }

    protected static function booted()
    {
        static::deleting(function ($model) {
            if ($model->cv_file) {
                ImageUpload::deleteApplicationStorage($model->cv_file);
            }
        });
    }
}