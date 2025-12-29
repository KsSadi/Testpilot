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
        Schema::create('generated_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_case_id')->constrained()->onDelete('cascade');
            $table->longText('code');
            $table->timestamp('generated_at');
            $table->timestamps();
            
            $table->index('test_case_id');
            $table->index('generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_codes');
    }
};
