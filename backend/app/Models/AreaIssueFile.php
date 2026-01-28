<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaIssueFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_issue_id',
        'file_path',
        'file_url',
        'file_type',
        'file_name',
        'file_size',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function issue()
    {
        return $this->belongsTo(AreaIssue::class, 'area_issue_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}