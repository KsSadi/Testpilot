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
        Schema::table('ai_providers', function (Blueprint $table) {
            $table->string('api_base_url')->nullable()->after('description');
            $table->json('api_keys')->nullable()->after('api_key'); // Multiple API keys for failover
            $table->integer('current_key_index')->default(0)->after('api_keys'); // Track which key to use
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_providers', function (Blueprint $table) {
            $table->dropColumn(['api_base_url', 'api_keys', 'current_key_index']);
        });
    }
};
