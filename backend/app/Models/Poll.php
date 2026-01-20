<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_en',
        'title_bn',
        'description_en',
        'description_bn',
        'poll_type',
        'target_audience',
        'ward_id',
        'thana_id',
        'is_anonymous',
        'allow_multiple_votes',
        'status',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
    ];

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class, 'thana_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

     public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}