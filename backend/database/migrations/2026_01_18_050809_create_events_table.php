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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('ward_id')->nullable();
            $table->unsignedInteger('thana_id')->nullable();
            
            
            $table->string('event_number', 50)->unique();

            $table->string('title_en', 500);
            $table->string('title_bn', 500)->nullable();

            $table->text('description')->nullable();

            $table->enum('event_type', [
                'meeting',
                'campaign',
                'awareness',
                'relief',
                'cultural',
                'sports',
            ])->nullable();

            $table->string('venue_en', 500)->nullable();
            $table->string('venue_bn', 500)->nullable();

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime')->nullable();

            $table->unsignedBigInteger('organizer_id');

            $table->integer('max_participants')->nullable();

            $table->boolean('registration_required')->default(false);
            $table->timestamp('registration_deadline')->nullable();

            $table->enum('status', [
                'scheduled',
                'ongoing',
                'completed',
                'cancelled',
            ])->default('scheduled');

            $table->string('banner_image', 500)->nullable();
            $table->string('banner_image_url', 500)->nullable();

            //who is created
            $table->unsignedBigInteger('created_by')->nullable();

            // Indexes
            $table->index('start_datetime');
            $table->index('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};