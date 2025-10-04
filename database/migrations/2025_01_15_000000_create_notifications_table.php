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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // tenant_added, enquiry_added, invoice_created, payment_received, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data for the notification
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('status')->default('pending'); // pending, sent, failed, cancelled
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('scheduled_at')->nullable(); // For scheduled notifications
            $table->morphs('notifiable'); // Polymorphic relationship (tenant, enquiry, invoice, etc.)
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['recipient_email', 'status']);
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
