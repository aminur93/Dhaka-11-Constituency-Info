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
        Schema::create('ward_commissioners', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();

            $table->bigInteger('ward_id')->unsigned();

            $table->string('commissioner_id', 50)->unique();

            $table->string('full_name_en', 200);
            $table->string('full_name_bn', 200)->nullable();

            $table->string('phone_en', 15);
            $table->string('phone_bn', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('nid_number_en', 17)->nullable();
            $table->string('nid_number_bn', 17)->nullable();
            $table->string('photo', 500)->nullable();
            $table->string('photo_url', 500)->nullable();

            $table->string('political_party', 100)->nullable();

            $table->date('term_start_date');
            $table->date('term_end_date')->nullable();

            $table->integer('election_year')->nullable();

            $table->tinyInteger('status', 20)->default(1)
                ->comment('active=1, inactive=0, resigned=2, terminated=3');

            $table->boolean('is_current')->default(true);
            $table->integer('created_by')->nullable();
            $table->timestamps();

            $table->index(['ward_id', 'is_current']);
            $table->index('commissioner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ward_commissioners');
    }
};