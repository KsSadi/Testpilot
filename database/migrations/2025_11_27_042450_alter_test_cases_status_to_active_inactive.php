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
        // First, temporarily change to VARCHAR to allow any value
        DB::statement("ALTER TABLE test_cases MODIFY COLUMN status VARCHAR(20) DEFAULT 'active'");

        // Update existing data to map old statuses to new ones
        DB::table('test_cases')->whereIn('status', ['pending', 'running'])->update(['status' => 'active']);
        DB::table('test_cases')->whereIn('status', ['completed', 'failed'])->update(['status' => 'inactive']);

        // Then alter the column to use new enum values
        DB::statement("ALTER TABLE test_cases MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
    }    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        Schema::table('test_cases', function (Blueprint $table) {
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending')->change();
        });
    }
};
