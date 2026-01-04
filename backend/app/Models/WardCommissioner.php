<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WardCommissioner extends Model
{
    protected $fillable = [
        'user_id',
        'ward_id',
        'commissioner_id',
        'full_name_en',
        'full_name_bn',
        'phone_en',
        'phone_bn',
        'email',
        'nid_number_en',
        'nid_number_bn',
        'photo',
        'photo_url',
        'political_party',
        'term_start_date',
        'term_end_date',
        'election_year',
        'status',
        'is_current',
        'created_by',
    ];

    public function details()
    {
        return $this->hasOne(WardCommissionerDetails::class, 'commissioner_id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}