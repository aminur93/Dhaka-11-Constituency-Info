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
        Schema::create('volunteer_tasks', function (Blueprint $table) {
            $table->id();
             // Task info
            $table->string('task_number', 50)->unique();
            $table->unsignedBigInteger('volunteer_id');
            $table->unsignedBigInteger('assigned_by');
            $table->unsignedBigInteger('related_request_id')->nullable();
            $table->string('task_type', 100)->nullable()->comment('verification, survey, awareness, relief_distribution, event');
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->string('priority', 20)->default('medium');
            $table->string('status', 30)->default('assigned')->comment('assigned, in_progress, completed, cancelled');

            // Location info
            $table->unsignedBigInteger('ward_id')->nullable();
            $table->text('location_details')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->date('deadline')->nullable();

            // Timing
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // whos is created
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('volunteer_id', 'idx_volunteer_id');
            $table->index('status', 'idx_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_tasks');
    }
};