<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL ENUM alteration via raw statement
        DB::statement("ALTER TABLE tenant_amenities MODIFY status ENUM('active','inactive','suspended','pending') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE tenant_amenities MODIFY status ENUM('active','inactive','suspended') DEFAULT 'active'");
    }
};
