<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceApplicant extends Model
{
    protected $fillable = [
        'user_id',
        'service_category_id',
        'ward_id',
        'union_id',
        'thana_id',
        'district_id',
        'division_id',
        'request_number',
        'priority',
        'status',
        'subject',
        'description',
        'requested_amount',
        'approved_amount',
        'assigned_to',
        'remarks',
        'rejection_reason',
        'completion_notes',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'completed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function serviceCategories()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class,'ward_id');
    }

    public function union()
    {
        return $this->belongsTo(Union::class,'union_id');
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class,'thana_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function attachments()
    {
        return $this->hasMany(ServiceApplicantAttachment::class, 'request_id');
    }

    public function statuses()
    {
        return $this->hasMany(ServiceApplicantStatus::class, 'request_id');
    }

    public function medicalDetails()
    {
        return $this->hasOne(MedicalAssistanceDetail::class,'request_id');
    }

    public function financialDetails()
    {
        return $this->hasOne(FinancialAidDetail::class,'request_id');
    }

    public function educationDetails()
    {
        return $this->hasOne(EducationSupportDetails::class,'request_id');
    }

    public function jobDetails()
    {
        return $this->hasOne(JobSkillRegistration::class,'request_id');
    }

    public function legalAidDetails()
    {
        return $this->hasOne(LegalAidDetails::class,'request_id');
    }

    public function disasterDetails()
    {
        return $this->hasOne(DisasterReliefDetail::class,'request_id');
    }
}