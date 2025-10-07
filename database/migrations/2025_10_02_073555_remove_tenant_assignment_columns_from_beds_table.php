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
        Schema::table('beds', function (Blueprint $table) {
            // Remove old tenant assignment columns
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'occupied_from', 'occupied_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beds', function (Blueprint $table) {
            // Add back the old columns
            $table->foreignId('tenant_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('occupied_from')->nullable();
            $table->date('occupied_until')->nullable();
        });
    }
};
