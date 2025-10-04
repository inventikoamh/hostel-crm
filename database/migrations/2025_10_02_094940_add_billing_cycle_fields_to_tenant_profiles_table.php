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
        Schema::table('tenant_profiles', function (Blueprint $table) {
            // Billing cycle configuration
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'half_yearly', 'yearly'])->default('monthly')->after('monthly_rent');
            $table->integer('billing_day')->default(1)->comment('Day of month for billing (1-31)')->after('billing_cycle');
            $table->date('next_billing_date')->nullable()->comment('Next scheduled billing date')->after('billing_day');
            $table->date('last_billing_date')->nullable()->comment('Last billing date')->after('next_billing_date');

            // Payment tracking
            $table->enum('payment_status', ['paid', 'pending', 'overdue', 'partial'])->default('pending')->after('last_billing_date');
            $table->date('last_payment_date')->nullable()->comment('Date of last payment received')->after('payment_status');
            $table->decimal('last_payment_amount', 10, 2)->nullable()->comment('Amount of last payment')->after('last_payment_date');
            $table->decimal('outstanding_amount', 10, 2)->default(0)->comment('Outstanding/overdue amount')->after('last_payment_amount');

            // Notification preferences
            $table->boolean('auto_billing_enabled')->default(true)->comment('Enable automatic billing notifications')->after('outstanding_amount');
            $table->json('notification_preferences')->nullable()->comment('Notification settings (email, sms, days_before)')->after('auto_billing_enabled');
            $table->integer('reminder_days_before')->default(3)->comment('Days before billing to send reminder')->after('notification_preferences');
            $table->integer('overdue_grace_days')->default(5)->comment('Grace period before marking overdue')->after('reminder_days_before');

            // Late fees and penalties
            $table->decimal('late_fee_amount', 8, 2)->nullable()->comment('Fixed late fee amount')->after('overdue_grace_days');
            $table->decimal('late_fee_percentage', 5, 2)->nullable()->comment('Late fee as percentage of rent')->after('late_fee_amount');
            $table->boolean('compound_late_fees')->default(false)->comment('Whether to compound late fees')->after('late_fee_percentage');

            // Billing history tracking
            $table->integer('consecutive_on_time_payments')->default(0)->comment('Count of consecutive on-time payments')->after('compound_late_fees');
            $table->integer('total_late_payments')->default(0)->comment('Total count of late payments')->after('consecutive_on_time_payments');
            $table->date('last_reminder_sent')->nullable()->comment('Date when last reminder was sent')->after('total_late_payments');
            $table->integer('reminder_count_current_cycle')->default(0)->comment('Number of reminders sent for current billing cycle')->after('last_reminder_sent');

            // Auto-payment settings (for future implementation)
            $table->boolean('auto_payment_enabled')->default(false)->comment('Enable automatic payment processing')->after('reminder_count_current_cycle');
            $table->string('payment_method')->nullable()->comment('Preferred payment method')->after('auto_payment_enabled');
            $table->json('payment_details')->nullable()->comment('Encrypted payment method details')->after('payment_method');

            // Indexes for performance
            $table->index(['next_billing_date']);
            $table->index(['payment_status']);
            $table->index(['billing_cycle']);
            $table->index(['auto_billing_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_profiles', function (Blueprint $table) {
            $table->dropIndex(['next_billing_date']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['billing_cycle']);
            $table->dropIndex(['auto_billing_enabled']);

            $table->dropColumn([
                'billing_cycle',
                'billing_day',
                'next_billing_date',
                'last_billing_date',
                'payment_status',
                'last_payment_date',
                'last_payment_amount',
                'outstanding_amount',
                'auto_billing_enabled',
                'notification_preferences',
                'reminder_days_before',
                'overdue_grace_days',
                'late_fee_amount',
                'late_fee_percentage',
                'compound_late_fees',
                'consecutive_on_time_payments',
                'total_late_payments',
                'last_reminder_sent',
                'reminder_count_current_cycle',
                'auto_payment_enabled',
                'payment_method',
                'payment_details'
            ]);
        });
    }
};
