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
        Schema::table('test_cases', function (Blueprint $table) {
            $table->string('session_id')->unique()->after('id');
        });
        
        // Generate unique session IDs for existing test cases
        DB::table('test_cases')->orderBy('id')->get()->each(function ($testCase) {
            DB::table('test_cases')
                ->where('id', $testCase->id)
                ->update(['session_id' => 'tc_' . time() . '_' . $testCase->id]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_cases', function (Blueprint $table) {
            $table->dropColumn('session_id');
        });
    }
};
