<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Union extends Model
{
    protected $fillable = [
        'thana_id',
        'name_en',
        'name_bn',
        'code',
        'is_active',
        'created_by',
    ];

    public function thana()
    {
        return $this->belongsTo(Thana::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}