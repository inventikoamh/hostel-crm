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
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('bed_number');
            $table->enum('bed_type', ['single', 'double', 'bunk_top', 'bunk_bottom'])->default('single');
            $table->enum('status', ['available', 'occupied', 'maintenance', 'reserved'])->default('available');
            $table->foreignId('tenant_id')->nullable()->constrained('users')->onDelete('set null'); // Current occupant
            $table->date('occupied_from')->nullable();
            $table->date('occupied_until')->nullable();
            $table->decimal('monthly_rent', 8, 2)->nullable(); // Individual bed rent if different from room
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('coordinates')->nullable(); // For room layout positioning {x, y}
            $table->timestamps();

            $table->unique(['room_id', 'bed_number']);
            $table->index(['status']);
            $table->index(['tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};
