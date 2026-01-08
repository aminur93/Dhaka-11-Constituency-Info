<?php

namespace App\Models;

use App\Helper\ImageUpload;
use Illuminate\Database\Eloquent\Model;

class FinancialAidDetail extends Model
{
    protected $fillable = [
        'request_id',
        'aid_purpose',
        'monthly_income',
        'family_members',
        'earning_members',
        'current_debt',
        'assets_description',
        'income_proof_file',
        'income_proof_file_url',
    ];

    public function serviceApplicant()
    {
        return $this->belongsTo(ServiceApplicant::class, 'request_id');
    }

    protected static function booted()
    {
        static::deleting(function ($model) {
            if ($model->income_proof_file) {
                ImageUpload::deleteApplicationStorage($model->income_proof_file);
            }
        });
    }
}