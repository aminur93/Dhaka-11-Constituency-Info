<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'attendance_status',
        'registered_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}