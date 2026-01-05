<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medical_assistance_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('request_id')->unsigned();
            $table->string('patient_name', 200);
            $table->integer('patient_age')->nullable();
            $table->string('patient_gender', 10)->nullable();
            $table->string('relation_with_applicant', 50)->nullable();
            $table->string('disease_type', 200)->nullable();
            $table->string('hospital_name', 300)->nullable();
            $table->string('doctor_name', 200)->nullable();
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->string('treatment_duration', 100)->nullable();
            $table->boolean('is_emergency')->default(false);
            $table->string('prescription_file', 500)->nullable();
            $table->string('prescription_file_url', 500)->nullable();
            $table->string('medical_report_file', 500)->nullable();
            $table->string('medical_report_file_url', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_assistance_details');
    }
};