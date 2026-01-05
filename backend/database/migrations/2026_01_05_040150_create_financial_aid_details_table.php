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
        Schema::create('financial_aid_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('request_id')->unsigned();

            $table->string('aid_purpose', 200);
            $table->decimal('monthly_income', 12, 2)->nullable();
            $table->integer('family_members')->nullable();
            $table->integer('earning_members')->nullable();
            $table->decimal('current_debt', 12, 2)->nullable();
            $table->text('assets_description')->nullable();
            $table->string('income_proof_file', 500)->nullable();
            $table->string('income_proof_file_url', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_aid_details');
    }
};