<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'question_en',
        'question_bn',
        'answer_en',
        'answer_bn',
        'display_order',
        'status',
        'view_count',
        'created_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'display_order' => 'integer',
        'view_count' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}