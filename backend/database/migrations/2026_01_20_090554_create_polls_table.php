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
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ward_id')->nullable();
            $table->unsignedInteger('thana_id')->nullable();
            
            $table->string('title_en', 500);
            $table->string('title_bn', 500)->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_bn')->nullable();

            $table->string('poll_type', 50)
                ->comment('opinion, feedback, survey, voting');

            $table->string('target_audience', 100)
                ->comment('all, ward_specific, thana_specific');

            $table->boolean('is_anonymous')->default(false);
            $table->boolean('allow_multiple_votes')->default(false);
            $table->boolean('status')->default(true);

            $table->timestamp('start_date')->useCurrent();
            $table->timestamp('end_date')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Indexes
            $table->index('status');
            $table->index('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};