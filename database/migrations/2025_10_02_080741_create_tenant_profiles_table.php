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
        Schema::create('tenant_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('occupation')->nullable();
            $table->string('company')->nullable();
            $table->enum('id_proof_type', ['aadhar', 'passport', 'driving_license', 'voter_id', 'pan_card', 'other'])->nullable();
            $table->string('id_proof_number')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending', 'suspended', 'moved_out'])->default('pending');
            $table->date('move_in_date')->nullable();
            $table->date('move_out_date')->nullable();
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->decimal('monthly_rent', 10, 2)->nullable();
            $table->date('lease_start_date')->nullable();
            $table->date('lease_end_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('documents')->nullable(); // Store document file paths
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['status']);
            $table->index(['move_in_date']);
            $table->index(['is_verified']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_profiles');
    }
};
