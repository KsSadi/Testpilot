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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Unique setting key');
            $table->text('value')->nullable()->comment('Setting value (can be JSON)');
            $table->string('type')->default('string')->comment('Data type: string, boolean, integer, array, json');
            $table->string('group')->default('general')->comment('Setting category/group');
            $table->string('label')->nullable()->comment('Human readable label');
            $table->text('description')->nullable()->comment('Setting description/help text');
            $table->boolean('is_active')->default(true)->comment('Whether setting is active');
            $table->boolean('is_encrypted')->default(false)->comment('Whether value should be encrypted');
            $table->integer('order')->default(0)->comment('Display order');
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index(['group', 'is_active']);
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
