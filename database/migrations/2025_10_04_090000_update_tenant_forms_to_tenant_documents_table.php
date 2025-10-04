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
        Schema::rename('tenant_forms', 'tenant_documents');

        Schema::table('tenant_documents', function (Blueprint $table) {
            // Add new columns for document system
            $table->enum('document_type', [
                'aadhar_card',
                'pan_card',
                'student_id',
                'tenant_agreement',
                'lease_agreement',
                'rental_agreement',
                'maintenance_form',
                'identity_proof',
                'address_proof',
                'income_proof',
                'other'
            ])->after('form_type')->default('other');

            $table->text('description')->nullable()->after('document_type');
            $table->enum('request_type', ['admin_upload', 'tenant_upload'])->default('tenant_upload')->after('description');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('request_type');
            $table->text('rejection_reason')->nullable()->after('approval_status');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('rejection_reason');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->timestamp('expiry_date')->nullable()->after('approved_at');
            $table->boolean('is_required')->default(false)->after('expiry_date');
            $table->integer('priority')->default(1)->after('is_required')->comment('1=Low, 2=Medium, 3=High');

            // Rename existing columns for better clarity
            $table->renameColumn('form_type', 'category');
            $table->renameColumn('form_number', 'document_number');
            $table->renameColumn('form_data', 'document_data');
            $table->renameColumn('signed_form_path', 'document_path');
            $table->renameColumn('uploaded_by', 'uploaded_by_admin');
            $table->renameColumn('uploaded_at', 'uploaded_at_admin');

            // Update status enum to include new statuses
            $table->dropColumn('status');
        });

        Schema::table('tenant_documents', function (Blueprint $table) {
            $table->enum('status', ['draft', 'requested', 'uploaded', 'approved', 'rejected', 'expired', 'archived'])->default('draft')->after('priority');
        });

        // Add indexes for better performance
        Schema::table('tenant_documents', function (Blueprint $table) {
            $table->index(['document_type', 'approval_status']);
            $table->index(['request_type', 'status']);
            $table->index(['is_required', 'priority']);
            $table->index(['expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_documents', function (Blueprint $table) {
            $table->dropIndex(['document_type', 'approval_status']);
            $table->dropIndex(['request_type', 'status']);
            $table->dropIndex(['is_required', 'priority']);
            $table->dropIndex(['expiry_date']);

            $table->dropColumn([
                'document_type',
                'description',
                'request_type',
                'approval_status',
                'rejection_reason',
                'approved_by',
                'approved_at',
                'expiry_date',
                'is_required',
                'priority',
                'status'
            ]);

            $table->renameColumn('category', 'form_type');
            $table->renameColumn('document_number', 'form_number');
            $table->renameColumn('document_data', 'form_data');
            $table->renameColumn('document_path', 'signed_form_path');
            $table->renameColumn('uploaded_by_admin', 'uploaded_by');
            $table->renameColumn('uploaded_at_admin', 'uploaded_at');
        });

        Schema::rename('tenant_documents', 'tenant_forms');
    }
};
