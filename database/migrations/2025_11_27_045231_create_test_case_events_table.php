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
        Schema::create('test_case_events', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('event_type');
            $table->text('selector')->nullable();
            $table->string('tag_name')->nullable();
            $table->string('url')->nullable();
            $table->text('value')->nullable();
            $table->text('inner_text')->nullable();
            $table->json('attributes')->nullable();
            $table->json('event_data')->nullable();
            $table->boolean('is_saved')->default(false);
            $table->timestamps();
            
            $table->foreign('session_id')
                  ->references('session_id')
                  ->on('test_cases')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_case_events');
    }
};
