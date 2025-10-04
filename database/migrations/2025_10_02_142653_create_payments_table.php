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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_profile_id')->constrained()->onDelete('cascade');

            // Payment details
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'upi', 'card', 'cheque', 'other'])->default('cash');
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed');

            // Payment method specific details
            $table->string('reference_number')->nullable()->comment('Transaction ID, Cheque number, etc.');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();

            // Additional details
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable()->comment('Additional payment data');

            // Tracking
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Indexes
            $table->index(['invoice_id', 'status']);
            $table->index(['tenant_profile_id', 'payment_date']);
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
