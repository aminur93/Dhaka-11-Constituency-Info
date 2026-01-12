<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class volunteer extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'volunteer_id',
        'designation',
        'specialization',
        'education',
        'profession',
        'blood_group',
        'emergency_contact',
        'availability',
        'skills',
        'languages_spoken',
        'volunteer_since',
        'status',
        'rating',
        'total_tasks_completed',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}