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
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // openai, gemini, deepseek
            $table->string('display_name'); // OpenAI, Google Gemini, DeepSeek
            $table->text('description')->nullable();
            $table->string('api_key')->nullable(); // Encrypted API key
            $table->json('models')->nullable(); // Available models for this provider
            $table->string('default_model')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_enabled')->default(true); // Can be toggled on/off
            $table->json('settings')->nullable(); // Provider-specific settings
            $table->integer('priority')->default(0); // For fallback priority
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_providers');
    }
};
