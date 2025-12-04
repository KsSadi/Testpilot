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
        Schema::table('project_shares', function (Blueprint $table) {
            // Rename project_id to shareable_id and add shareable_type
            $table->renameColumn('project_id', 'shareable_id');
            $table->string('shareable_type')->after('id')->default('App\\Modules\\Cypress\\Models\\Project');
            
            // Add index for polymorphic relationship
            $table->index(['shareable_type', 'shareable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_shares', function (Blueprint $table) {
            $table->dropIndex(['shareable_type', 'shareable_id']);
            $table->dropColumn('shareable_type');
            $table->renameColumn('shareable_id', 'project_id');
        });
    }
};
