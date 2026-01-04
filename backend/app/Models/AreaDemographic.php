<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaDemographic extends Model
{
    protected $fillable = [
        'ward_id',
        'thana_id',
        'total_population',
        'male_population',
        'female_population',
        'age_0_18',
        'age_19_35',
        'age_36_60',
        'age_above_60',
        'total_voters',
        'literacy_rate',
        'avg_income',
        'updated_year',
        'created_by',
    ];

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}