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
        Schema::table('test_cases', function (Blueprint $table) {
            // Add status column
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending')->after('order');
            
            // Add session_data column
            $table->json('session_data')->nullable()->after('status');
            
            // Remove url column if it exists
            if (Schema::hasColumn('test_cases', 'url')) {
                $table->dropColumn('url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_cases', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn(['status', 'session_data']);
            
            // Add back url column
            $table->text('url')->nullable()->after('name');
        });
    }
};
