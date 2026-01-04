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
        Schema::create('ward_commissioner_details', function (Blueprint $table) {
            $table->id();
             $table->bigInteger('commissioner_id')->unsigned();

            $table->date('date_of_birth')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('blood_group', 5)->nullable();

            $table->string('education', 500)->nullable();
            $table->string('profession', 200)->nullable();

            $table->text('previous_experience')->nullable();
            $table->text('achievements')->nullable();
            $table->text('social_activities')->nullable();

            $table->string('emergency_contact', 15)->nullable();

            $table->text('permanent_address_en')->nullable();
            $table->text('permanent_address_bn')->nullable();
            $table->text('present_address_en')->nullable();
            $table->text('present_address_bn')->nullable();

            $table->string('facebook_url', 500)->nullable();
            $table->string('twitter_url', 500)->nullable();

            $table->string('office_address_en', 500)->nullable();
            $table->string('office_address_bn', 500)->nullable();
            $table->string('office_phone_en', 15)->nullable();
            $table->string('office_phone_bn', 15)->nullable();
            $table->string('office_hours', 200)->nullable()
                ->comment('e.g., Sat-Thu 10AM-5PM');
            $table->string('public_meeting_schedule', 500)->nullable()
                ->comment('e.g., Every Monday 3PM');

            $table->text('biography_en')->nullable();
            $table->text('biography_bn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ward_commissioner_details');
    }
};