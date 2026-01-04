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
        Schema::create('area_demographics', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ward_id')->unsigned();
            $table->bigInteger('thana_id')->unsigned();
            $table->bigInteger('total_population');
            $table->bigInteger('male_population');
            $table->bigInteger('female_population');
            $table->integer('age_0_18');
            $table->integer('age_19_35');
            $table->integer('age_36_60');
            $table->integer('age_above_60');
            $table->bigInteger('total_voters');
            $table->decimal('literacy_rate', 5, 2);
            $table->decimal('avg_income', 12, 2);
            $table->integer('updated_year');
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_demographics');
    }
};