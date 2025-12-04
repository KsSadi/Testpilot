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
        Schema::table('test_case_events', function (Blueprint $table) {
            $table->integer('event_order')->default(0)->after('is_saved');
            $table->text('comment')->nullable()->after('event_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_case_events', function (Blueprint $table) {
            $table->dropColumn(['event_order', 'comment']);
        });
    }
};
