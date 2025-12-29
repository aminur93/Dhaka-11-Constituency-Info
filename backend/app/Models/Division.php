<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = [
        'name_en',
        'name_bn',
        'code',
        'is_active',
    ];

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}