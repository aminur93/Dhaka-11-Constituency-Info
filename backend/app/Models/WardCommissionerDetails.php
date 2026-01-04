<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WardCommissionerDetails extends Model
{
    protected $fillable = [
        'commissioner_id',
        'date_of_birth',
        'gender',
        'blood_group',
        'education',
        'profession',
        'previous_experience',
        'achievements',
        'social_activities',
        'emergency_contact',
        'permanent_address_en',
        'permanent_address_bn',
        'present_address_en',
        'present_address_bn',
        'facebook_url',
        'twitter_url',
        'office_address_en',
        'office_address_bn',
        'office_phone_en',
        'office_phone_bn',
        'office_hours',
        'public_meeting_schedule',
        'biography_en',
        'biography_bn',
    ];

    public function commissioner()
    {
        return $this->belongsTo(WardCommissioner::class, 'commissioner_id');
    }
}