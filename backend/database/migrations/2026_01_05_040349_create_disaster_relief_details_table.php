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
        Schema::create('disaster_relief_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('request_id')->unsigned();

            $table->string('disaster_type', 100)->nullable();
            $table->date('disaster_date')->nullable();
            $table->text('loss_type')->nullable();
            $table->decimal('estimated_loss', 12, 2)->nullable();
            $table->integer('family_affected')->nullable();
            $table->boolean('temporary_shelter_needed')->default(false);
            $table->text('relief_items_needed')->nullable();
            $table->string('damage_photo', 500)->nullable();
            $table->string('damage_photo_url', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disaster_relief_details');
    }
};