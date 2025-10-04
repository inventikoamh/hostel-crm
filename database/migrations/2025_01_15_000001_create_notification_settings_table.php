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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('notification_type'); // tenant_added, enquiry_added, invoice_created, etc.
            $table->string('name'); // Human readable name
            $table->text('description')->nullable();
            $table->boolean('enabled')->default(true);
            $table->string('recipient_type'); // admin, tenant, specific_email
            $table->string('recipient_email')->nullable(); // For specific_email type
            $table->json('email_template')->nullable(); // Template configuration
            $table->json('conditions')->nullable(); // Conditions for sending
            $table->integer('priority')->default(1); // 1=high, 2=medium, 3=low
            $table->boolean('send_immediately')->default(true);
            $table->integer('delay_minutes')->default(0); // Delay before sending
            $table->timestamps();

            $table->unique(['notification_type', 'recipient_type', 'recipient_email'], 'notif_settings_unique');
            $table->index(['enabled', 'notification_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
