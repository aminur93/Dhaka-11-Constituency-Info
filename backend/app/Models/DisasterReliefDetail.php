<?php

namespace App\Models;

use App\Helper\ImageUpload;
use Illuminate\Database\Eloquent\Model;

class DisasterReliefDetail extends Model
{
    protected $fillable = [
        'request_id',
        'disaster_type',
        'disaster_date',
        'loss_type',
        'estimated_loss',
        'family_affected',
        'temporary_shelter_needed',
        'relief_items_needed',
        'damage_photo',
        'damage_photo_url',
    ];

    protected static function booted()
    {
        static::deleting(function ($model) {
            if ($model->damage_photo) {
                ImageUpload::deleteApplicationStorage($model->damage_photo);
            }
        });
    }
}