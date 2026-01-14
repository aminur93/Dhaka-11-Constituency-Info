<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldReportMedia extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'report_id',
        'media_type',
        'file_path',
        'file_path_url',
        'caption',
        'latitude',
        'longitude',
        'uploaded_at',
        'created_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'latitude'    => 'decimal:8',
        'longitude'   => 'decimal:8',
    ];

    public function fieldReport()
    {
        return $this->belongsTo(FieldReport::class, 'report_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}