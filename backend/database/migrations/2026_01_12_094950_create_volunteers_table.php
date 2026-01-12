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
        Schema::create('volunteers', function (Blueprint $table) {
            $table->id();

             // Foreign key
            $table->unsignedBigInteger('user_id');

            // Volunteer info
            $table->string('volunteer_id', 50)->unique();
            $table->string('designation', 100)->nullable();
            $table->string('specialization', 200)->nullable();
            $table->string('education', 200)->nullable();
            $table->string('profession', 200)->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('emergency_contact', 15)->nullable();
            $table->string('availability', 500)->nullable()->comment('days/hours available');
            $table->text('skills')->nullable();
            $table->string('languages_spoken', 200)->nullable();
            $table->date('volunteer_since')->nullable();
            $table->string('status', 20)->default('active')->comment('active, inactive, suspended');
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('total_tasks_completed')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteers');
    }
};