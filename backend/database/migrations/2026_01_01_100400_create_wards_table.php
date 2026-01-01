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
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('thana_id')->unsigned();
            $table->bigInteger('union_id')->unsigned()->nullable();
            $table->string('name_en', 100);
            $table->string('name_bn', 100);
            $table->string('ward_number');
            $table->enum('area_type', ['urban', 'rural']);
            $table->integer('population_estimate');
            $table->integer('total_households');
            $table->boolean('is_active')->default(true);
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};