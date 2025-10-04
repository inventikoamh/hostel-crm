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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('tenant_profile_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['rent', 'amenities', 'damage', 'other'])->default('rent');
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');

            // Invoice details
            $table->date('invoice_date');
            $table->date('due_date');
            $table->date('period_start')->nullable()->comment('For recurring charges like rent');
            $table->date('period_end')->nullable()->comment('For recurring charges like rent');

            // Financial details
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);

            // Additional details
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->json('metadata')->nullable()->comment('Additional data like room info, amenity details');

            // Payment tracking
            $table->date('paid_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            $table->timestamps();

            // Indexes
            $table->index(['tenant_profile_id', 'status']);
            $table->index(['invoice_date', 'due_date']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
