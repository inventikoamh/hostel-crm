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
        Schema::table('hostels', function (Blueprint $table) {
            $table->dropColumn(['total_rooms', 'total_beds', 'rent_per_bed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->integer('total_rooms')->nullable();
            $table->integer('total_beds')->nullable();
            $table->decimal('rent_per_bed', 10, 2)->nullable();
        });
    }
};
