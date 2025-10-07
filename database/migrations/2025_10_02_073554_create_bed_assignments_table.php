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
        Schema::create('bed_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bed_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
            $table->date('assigned_from');
            $table->date('assigned_until')->nullable();
            $table->enum('status', ['active', 'reserved', 'completed', 'cancelled'])->default('active');
            $table->decimal('monthly_rent', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['bed_id', 'status']);
            $table->index(['tenant_id', 'status']);
            $table->index(['assigned_from', 'assigned_until']);
            $table->index(['status']);

            // Ensure no overlapping assignments for the same bed
            $table->unique(['bed_id', 'assigned_from', 'assigned_until'], 'unique_bed_assignment_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bed_assignments');
    }
};
