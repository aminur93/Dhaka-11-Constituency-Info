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
        Schema::create('service_applicants', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();

            $table->bigInteger('service_category_id')->unsigned();

            $table->bigInteger('ward_id')->unsigned()->nullable();
            $table->bigInteger('union_id')->unsigned()->nullable();
            $table->bigInteger('thana_id')->unsigned()->nullable();
            $table->bigInteger('district_id')->unsigned()->nullable();
            $table->bigInteger('division_id')->unsigned()->nullable();
            $table->string('request_number', 50)->nullable();
            $table->string('priority', 20)
                ->default('medium')
                ->comment('low, medium, high, urgent');

            $table->string('status', 30)
                ->default('pending')
                ->comment('pending, in_review, approved, in_progress, completed, rejected, cancelled');

            $table->string('subject', 500);
            $table->text('description');

            $table->decimal('requested_amount', 12, 2)->nullable();
            $table->decimal('approved_amount', 12, 2)->nullable();

            $table->bigInteger('assigned_to')->unsigned()->nullable();

            $table->text('remarks')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('completion_notes')->nullable();

            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('service_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_applicants');
    }
};