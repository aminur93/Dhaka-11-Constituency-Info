<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_code',
        'issue_category_id',
        'ward_id',
        'thana_id',
        'reported_by',
        'title_en',
        'title_bn',
        'description_en',
        'description_bn',
        'severity',
        'status',
        'latitude',
        'longitude',
        'photo',
        'photo_url',
        'source',
        'is_anonymous',
        'assigned_to',
        'priority_score',
        'resolved_at',
        'reported_at',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'resolved_at' => 'datetime',
        'reported_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(IssueCategory::class, 'issue_category_id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function areaIssueFile()
    {
        return $this->hasMany(AreaIssueFile::class);
    }
}