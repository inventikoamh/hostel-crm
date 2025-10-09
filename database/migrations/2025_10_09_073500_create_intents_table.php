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
        Schema::create('intents', function (Blueprint $table) {
            $table->id();
            $table->string('intent_name')->unique();
            $table->string('intent_api_url');
            $table->string('intent_api_type'); // GET, POST, PUT, PATCH, DELETE
            $table->json('intent_api_parameters')->nullable(); // Store parameters as JSON
            $table->text('intent_description');
            $table->string('module')->nullable(); // Which module this intent belongs to
            $table->boolean('requires_auth')->default(true); // Whether authentication is required
            $table->timestamps();
            
            $table->index(['module', 'intent_api_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intents');
    }
};
