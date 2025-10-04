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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');

            // Item details
            $table->string('item_type')->comment('rent, amenity, damage, other');
            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 8, 2);
            $table->decimal('total_price', 10, 2);

            // Reference to related models
            $table->foreignId('related_id')->nullable()->comment('ID of related model (room, amenity, etc.)');
            $table->string('related_type')->nullable()->comment('Type of related model');

            // Period for recurring items
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();

            // Additional details
            $table->json('metadata')->nullable()->comment('Additional item-specific data');

            $table->timestamps();

            // Indexes
            $table->index(['invoice_id', 'item_type']);
            $table->index(['related_id', 'related_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
