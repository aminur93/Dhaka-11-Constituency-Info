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
        Schema::create('volunteer_area_assignments', function (Blueprint $table) {
            $table->id();
             // Foreign keys
            $table->unsignedBigInteger('volunteer_id');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->unsignedBigInteger('ward_id')->nullable();
            $table->unsignedBigInteger('thana_id')->nullable();

            // Assignment info
            $table->boolean('is_primary')->default(false);
            $table->timestamp('assigned_at')->useCurrent();

            // whos is created
            $table->unsignedBigInteger('created_by')->nullable();

            // Indexes
            $table->unique(['volunteer_id', 'ward_id'], 'uq_volunteer_ward');
            $table->index('volunteer_id');
            $table->index('ward_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_area_assignments');
    }
};