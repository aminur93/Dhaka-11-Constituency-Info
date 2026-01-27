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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 100)->nullable()->comment('service, process, eligibility, contact');

            $table->string('question_en', 1000);
            $table->string('question_bn', 1000)->nullable();

            $table->text('answer_en');
            $table->text('answer_bn')->nullable();

            $table->integer('display_order')->nullable();

            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('view_count')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};