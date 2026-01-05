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
        Schema::create('education_support_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('request_id')->unsigned();
            $table->string('student_name', 200);
            $table->integer('student_age')->nullable();
            $table->string('education_level', 100)->nullable();
            $table->string('institution_name', 300)->nullable();
            $table->string('class_year', 50)->nullable();
            $table->decimal('gpa_cgpa', 4, 2)->nullable();
            $table->string('support_type', 100)->nullable();
            $table->string('academic_certificate_file', 500)->nullable();
            $table->string('academic_certificate_file_url', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_support_details');
    }
};