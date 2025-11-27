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
        Schema::table('projects', function (Blueprint $table) {
            // Add status column
            $table->enum('status', ['active', 'inactive'])->default('active')->after('description');
            
            // Rename user_id to created_by
            $table->renameColumn('user_id', 'created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Remove status column
            $table->dropColumn('status');
            
            // Rename created_by back to user_id
            $table->renameColumn('created_by', 'user_id');
        });
    }
};
