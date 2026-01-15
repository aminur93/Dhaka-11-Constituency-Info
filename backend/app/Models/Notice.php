<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'ward_id',
        'thana_id',
        'title_en',
        'title_bn',
        'content_en',
        'content_bn',
        'category',
        'priority',
        'target_audience',
        'is_active',
        'attachment_file',
        'attachment_file_url',
        'published_at',
        'expires_at',
        'created_by',
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
}