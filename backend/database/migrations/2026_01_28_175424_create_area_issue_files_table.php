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
        Schema::create('area_issue_files', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('area_issue_id');

            $table->string('file_path', 500); // storage path
            $table->string('file_url', 500); // storage path
            $table->string('file_type', 50)->nullable(); // image, video, document
            $table->string('file_name')->nullable(); // original file name
            $table->integer('file_size')->nullable(); // in KB

            $table->unsignedBigInteger('uploaded_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_issue_files');
    }
};