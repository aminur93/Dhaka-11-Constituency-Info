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
        Schema::create('service_applicant_statuses', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('request_id')->unsigned();

            $table->string('old_status', 30)->nullable();
            $table->string('new_status', 30);

            $table->bigInteger('changed_by')->unsigned();

            $table->text('remarks')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_applicant_statuses');
    }
};
