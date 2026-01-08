<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceApplicantStatus extends Model
{
    protected $fillable = [
        'request_id',
        'old_status',
        'new_status',
        'changed_by',
        'remarks',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime'
    ];

    public function serviceApplicant()
    {
        return $this->belongsTo(ServiceApplicant::class, 'request_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}