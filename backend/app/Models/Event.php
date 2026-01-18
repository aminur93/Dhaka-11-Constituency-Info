<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_number',
        'title_en',
        'title_bn',
        'description',
        'event_type',
        'venue_en',
        'venue_bn',
        'ward_id',
        'thana_id',
        'latitude',
        'longitude',
        'start_datetime',
        'end_datetime',
        'organizer_id',
        'max_participants',
        'registration_required',
        'registration_deadline',
        'status',
        'banner_image',
        'banner_image_url',
        'created_by'
    ];

    protected $casts = [
        'registration_required' => 'boolean',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'registration_deadline' => 'datetime',
    ];

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class, 'thana_id');
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}