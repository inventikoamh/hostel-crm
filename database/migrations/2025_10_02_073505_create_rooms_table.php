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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained()->onDelete('cascade');
            $table->string('room_number');
            $table->string('room_type'); // single, double, triple, dormitory, etc.
            $table->integer('floor');
            $table->integer('capacity'); // Total number of beds
            $table->decimal('rent_per_bed', 8, 2);
            $table->enum('status', ['available', 'occupied', 'maintenance', 'reserved'])->default('available');
            $table->text('description')->nullable();
            $table->json('amenities')->nullable(); // Room-specific amenities
            $table->decimal('area_sqft', 8, 2)->nullable(); // Room area in square feet
            $table->boolean('has_attached_bathroom')->default(false);
            $table->boolean('has_balcony')->default(false);
            $table->boolean('has_ac')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('coordinates')->nullable(); // For map positioning {x, y, width, height}
            $table->timestamps();

            $table->unique(['hostel_id', 'room_number']);
            $table->index(['hostel_id', 'floor']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
