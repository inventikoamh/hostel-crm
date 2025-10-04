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
        Schema::create('tenant_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('paid_amenity_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('custom_price', 8, 2)->nullable()->comment('Override default price if needed');
            $table->json('custom_schedule')->nullable()->comment('Custom availability for this tenant');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tenant_profile_id', 'paid_amenity_id'], 'tenant_amenity_unique');
            $table->index(['status']);
            $table->index(['start_date']);
            $table->index(['end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_amenities');
    }
};
