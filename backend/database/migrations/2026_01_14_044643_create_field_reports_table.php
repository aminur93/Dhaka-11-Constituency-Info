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
        Schema::create('field_reports', function (Blueprint $table) {
            $table->id();
             // Foreign keys
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('volunteer_id');

            // Report details
            $table->string('report_title', 500)->nullable();
            $table->text('report_description');
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->integer('people_met')->nullable();

            // Location
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Submission time
            $table->timestamp('submitted_at')->useCurrent();

            // who is created
            $table->unsignedBigInteger('created_by')->nullable();

            // Indexes
            $table->index('task_id');
            $table->index('volunteer_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_reports');
    }
};