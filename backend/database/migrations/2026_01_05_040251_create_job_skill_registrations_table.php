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
        Schema::create('job_skill_registrations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('request_id')->unsigned();

            $table->string('qualification', 200)->nullable();
            $table->integer('experience_years')->nullable();
            $table->text('skills')->nullable();
            $table->string('preferred_sector', 200)->nullable();
            $table->text('training_interest')->nullable();
            $table->string('employment_status', 50)->nullable();
            $table->string('cv_file', 500)->nullable();
            $table->string('cv_file_url', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_skill_registrations');
    }
};