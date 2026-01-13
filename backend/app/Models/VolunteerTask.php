<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_number',
        'volunteer_id',
        'assigned_by',
        'related_request_id',
        'task_type',
        'title',
        'description',
        'priority',
        'status',
        'ward_id',
        'location_details',
        'latitude',
        'longitude',
        'deadline',
        'assigned_at',
        'started_at',
        'completed_at',
        'created_by',
    ];

    public function volunteer()
    {
        return $this->belongsTo(volunteer::class, 'volunteer_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function serviceApplication()
    {
        return $this->belongsTo(ServiceApplicant::class, 'related_request_id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}