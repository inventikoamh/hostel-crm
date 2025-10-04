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
        Schema::create('tenant_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_profile_id')->constrained()->onDelete('cascade');
            $table->string('form_type')->default('tenant_agreement'); // tenant_agreement, lease_agreement, etc.
            $table->string('form_number')->unique(); // Auto-generated form number
            $table->json('form_data'); // Store all tenant data for the form
            $table->string('printed_by')->nullable(); // Admin who printed the form
            $table->timestamp('printed_at')->nullable(); // When the form was printed
            $table->string('signed_form_path')->nullable(); // Path to uploaded signed form
            $table->string('uploaded_by')->nullable(); // Admin who uploaded the signed form
            $table->timestamp('uploaded_at')->nullable(); // When the signed form was uploaded
            $table->enum('status', ['draft', 'printed', 'signed', 'archived'])->default('draft');
            $table->text('notes')->nullable(); // Additional notes
            $table->timestamps();

            $table->index(['tenant_profile_id', 'status']);
            $table->index(['form_type', 'status']);
            $table->index(['printed_at']);
            $table->index(['uploaded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_forms');
    }
};
