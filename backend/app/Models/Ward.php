<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    protected $fillable = [
        'thana_id',
        'union_id',
        'name_en',
        'name_bn',
        'ward_number',
        'area_type',
        'population_estimate',
        'total_households',
        'is_active',
        'created_by',
    ];

    public function thana()
    {
        return $this->belongsTo(Thana::class);
    }

    public function union()
    {
        return $this->belongsTo(Union::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}