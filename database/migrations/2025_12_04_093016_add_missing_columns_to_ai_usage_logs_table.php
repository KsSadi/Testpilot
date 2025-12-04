<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_usage_logs', function (Blueprint $table) {
            // Add 'cost' as alias/duplicate of 'estimated_cost' for backwards compatibility
            $table->decimal('cost', 10, 6)->default(0)->after('total_tokens')->comment('Same as estimated_cost');
            // Add 'response_time' as alias for 'response_time_ms'
            $table->integer('response_time')->nullable()->after('cost')->comment('Same as response_time_ms');
        });
        
        // Copy data from estimated_cost to cost and response_time_ms to response_time
        DB::statement('UPDATE ai_usage_logs SET cost = estimated_cost, response_time = response_time_ms');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_usage_logs', function (Blueprint $table) {
            $table->dropColumn(['cost', 'response_time']);
        });
    }
};
