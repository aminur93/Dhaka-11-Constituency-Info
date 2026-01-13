<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerAreaAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer_id',
        'assigned_by',
        'ward_id',
        'thana_id',
        'is_primary',
        'assigned_at',
        'created_by',
    ];

    public function volunteer()
    {
        return $this->belongsTo(volunteer::class, 'volunteer_id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}