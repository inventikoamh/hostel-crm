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
        Schema::create('paid_amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('billing_type', ['monthly', 'daily'])->default('monthly');
            $table->decimal('price', 8, 2);
            $table->enum('category', ['food', 'cleaning', 'laundry', 'utilities', 'services', 'other'])->default('other');
            $table->boolean('is_active')->default(true);
            $table->json('availability_schedule')->nullable()->comment('Days/times when service is available');
            $table->integer('max_usage_per_day')->nullable()->comment('Maximum usage per day for daily billing');
            $table->text('terms_conditions')->nullable();
            $table->string('icon')->nullable()->comment('FontAwesome icon class');
            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['category']);
            $table->index(['billing_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paid_amenities');
    }
};
