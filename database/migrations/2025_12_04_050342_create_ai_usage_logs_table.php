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
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('provider'); // openai, gemini, deepseek
            $table->string('model'); // gpt-4, gemini-pro, etc.
            $table->string('feature'); // test_generation, code_optimization, etc.
            $table->text('prompt')->nullable();
            $table->longText('response')->nullable();
            $table->integer('prompt_tokens')->default(0);
            $table->integer('completion_tokens')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->decimal('estimated_cost', 10, 6)->default(0); // Cost in USD
            $table->integer('response_time_ms')->nullable(); // Response time in milliseconds
            $table->string('status')->default('success'); // success, error, timeout
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            // Indexes for analytics
            $table->index(['user_id', 'created_at']);
            $table->index(['provider', 'created_at']);
            $table->index(['feature', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};
