<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            DB::statement("UPDATE users SET user_type = 'admin' WHERE user_type NOT IN ('admin', 'manager', 'viewer', 'editor', 'subcription')");

            DB::statement("ALTER TABLE users DROP CONSTRAINT users_user_type_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_user_type_check 
        CHECK (user_type IN ('admin', 'manager', 'viewer', 'editor', 'subcription'))");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            DB::statement("ALTER TABLE users DROP CONSTRAINT users_user_type_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_user_type_check 
        CHECK (user_type IN ('owner', 'superadmin', 'admin', 'subcription'))");
        });
    }
};
