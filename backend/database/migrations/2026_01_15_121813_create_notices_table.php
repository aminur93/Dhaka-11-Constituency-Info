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
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ward_id')->nullable();
            $table->unsignedBigInteger('thana_id')->nullable();
            $table->string('title_en', 500);
            $table->string('title_bn', 500)->nullable();
            $table->text('content_en');
            $table->text('content_bn')->nullable();

            $table->string('category', 100)->nullable()->comment('announcement, alert, information, circular');
            $table->string('priority', 20)->default('normal');

            $table->string('target_audience', 100)->nullable()->comment('all, ward_specific, thana_specific');
           

            $table->boolean('is_active')->default(true);

            $table->string('attachment_file', 500)->nullable();
            $table->string('attachment_file_url', 500)->nullable();

            $table->timestamp('published_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            // Indexes
            $table->index('is_active');
            $table->index('published_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};