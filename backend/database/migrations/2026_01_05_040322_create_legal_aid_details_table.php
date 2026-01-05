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
        Schema::create('legal_aid_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('request_id')->unsigned();

            $table->string('case_type', 200)->nullable();
            $table->string('case_number', 100)->nullable();
            $table->string('court_name', 300)->nullable();
            $table->string('opponent_party', 500)->nullable();
            $table->text('case_description')->nullable();
            $table->string('case_documents_file', 500)->nullable();
            $table->string('case_documents_file_url', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_aid_details');
    }
};