<?php

namespace App\Models;

use App\Helper\ImageUpload;
use Illuminate\Database\Eloquent\Model;

class LegalAidDetails extends Model
{
    protected $fillable = [
        'request_id',
        'case_type',
        'case_number',
        'court_name',
        'opponent_party',
        'case_description',
        'case_documents_file',
        'case_documents_file_url',
    ];

     protected static function booted()
    {
        static::deleting(function ($model) {
            if ($model->case_documents_file) {
                ImageUpload::deleteApplicationStorage($model->case_documents_file);
            }
        });
    }
}