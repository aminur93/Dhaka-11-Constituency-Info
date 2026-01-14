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
        Schema::create('field_report_media', function (Blueprint $table) {
            $table->id();
            // Foreign key
            $table->unsignedBigInteger('report_id');

            // Media info
            $table->string('media_type', 20)->comment('photo, video, document');
            $table->string('file_path', 1000)->nullable();
            $table->string('file_path_url', 1000)->nullable();
            $table->text('caption')->nullable();

            // Location
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Upload time
            $table->timestamp('uploaded_at')->useCurrent();

            // who is created
            $table->unsignedBigInteger('created_by')->nullable();

            // Indexes
            $table->index('report_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_report_media');
    }
};