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
        Schema::create('event_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_case_id')->constrained('test_cases')->onDelete('cascade');
            $table->string('session_uuid')->unique(); // Unique session identifier
            $table->string('name')->nullable(); // Optional custom name
            $table->integer('version')->default(1); // Auto-incrementing version per test case
            $table->integer('events_count')->default(0);
            $table->timestamp('recorded_at')->nullable(); // When recording started
            $table->timestamps();
            
            $table->index(['test_case_id', 'version']);
        });
        
        // Add event_session_id to test_case_events table
        Schema::table('test_case_events', function (Blueprint $table) {
            $table->foreignId('event_session_id')->nullable()->after('session_id')->constrained('event_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_case_events', function (Blueprint $table) {
            $table->dropForeign(['event_session_id']);
            $table->dropColumn('event_session_id');
        });
        
        Schema::dropIfExists('event_sessions');
    }
};
