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
        Schema::create('service_applicant_attachments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('request_id')->unsigned();

            $table->string('file_name', 500);
            $table->string('file_path', 1000);
            $table->string('file_type', 50)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->bigInteger('uploaded_by')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_applicant_attachments');
    }
};
