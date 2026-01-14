<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'volunteer_id',
        'report_title',
        'report_description',
        'findings',
        'recommendations',
        'people_met',
        'latitude',
        'longitude',
        'submitted_at',
        'created_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'latitude'     => 'decimal:8',
        'longitude'    => 'decimal:8',
    ];

    public function task()
    {
        return $this->belongsTo(VolunteerTask::class, 'task_id');
    }

    public function volunteer()
    {
        return $this->belongsTo(volunteer::class, 'volunteer_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function media()
    {
        return $this->hasMany(FieldReportMedia::class, 'report_id');
    }
}