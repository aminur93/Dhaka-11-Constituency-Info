<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hero extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title_en',
        'title_bn',
        'sub_title_en',
        'sub_title_bn',
        'description_en',
        'description_bn',
        'image',
        'image_url',
        'status',
    ];
}