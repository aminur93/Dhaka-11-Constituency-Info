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
        Schema::create('issue_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->nullable();
            $table->string('name_bn')->nullable();
            $table->longText('description_en')->nullable();
            $table->longText('description_bn')->nullable();
            $table->string('image')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('status')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_categories');
    }
};