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
        Schema::create('tenant_amenity_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_amenity_id')->constrained()->onDelete('cascade');
            $table->date('usage_date');
            $table->integer('quantity')->default(1)->comment('Number of times used on this date');
            $table->decimal('unit_price', 8, 2)->comment('Price per unit on usage date');
            $table->decimal('total_amount', 8, 2)->comment('quantity * unit_price');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['usage_date']);
            $table->index(['tenant_amenity_id', 'usage_date']);
            $table->index(['recorded_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_amenity_usage');
    }
};
