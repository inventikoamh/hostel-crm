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
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('postal_code');
            $table->string('phone');
            $table->string('email');
            $table->string('website')->nullable();
            $table->integer('total_rooms');
            $table->integer('total_beds');
            $table->decimal('rent_per_bed', 10, 2);
            $table->json('amenities')->nullable(); // Store amenities as JSON
            $table->json('images')->nullable(); // Store image URLs as JSON
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->string('manager_name');
            $table->string('manager_phone');
            $table->string('manager_email');
            $table->text('rules')->nullable();
            $table->time('check_in_time')->default('14:00:00');
            $table->time('check_out_time')->default('11:00:00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostels');
    }
};
