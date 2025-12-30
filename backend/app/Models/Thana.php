<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thana extends Model
{
    protected $fillable = [
        'district_id',
        'name_en',
        'name_bn',
        'code',
        'constituency',
        'is_active',
        'created_by',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}