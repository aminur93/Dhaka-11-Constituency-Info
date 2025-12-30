<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCategory extends Model
{

    protected $fillable = [
        'name_en',
        'name_bn',
        'description_en',
        'description_bn',
        'image',
        'image_url',
        'status',
        'created_by',
    ];
}