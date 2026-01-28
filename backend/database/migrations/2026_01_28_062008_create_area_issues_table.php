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
        Schema::create('area_issues', function (Blueprint $table) {
            $table->id();
             $table->string('issue_code', 30)->unique();

            $table->foreignId('issue_category_id')->constrained('issue_categories')->cascadeOnDelete();
            $table->foreignId('ward_id')->constrained('wards')->cascadeOnDelete();
            $table->foreignId('thana_id')->constrained('thanas')->cascadeOnDelete();

            $table->unsignedBigInteger('reported_by')->nullable();

            $table->string('title_en', 500);
            $table->string('title_bn', 500)->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_bn')->nullable();

            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');

            $table->enum('status', [
                'reported',
                'verified',
                'assigned',
                'in_progress',
                'resolved',
                'closed',
                'rejected'
            ])->default('reported');

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->string('photo', 500)->nullable();
            $table->string('photo_url', 500)->nullable();

            $table->string('source', 30)->default('app'); // app/web/admin/hotline
            $table->boolean('is_anonymous')->default(false);

            $table->unsignedBigInteger('assigned_to')->nullable();

            $table->integer('priority_score')->default(0);

            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('reported_at')->useCurrent();

            $table->timestamps(); // updated_at included

            // Indexes for filtering
            $table->index(['ward_id']);
            $table->index(['issue_category_id']);
            $table->index(['status']);
            $table->index(['severity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_issues');
    }
};