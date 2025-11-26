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
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('identifier'); // email or mobile
            $table->string('otp', 6);
            $table->enum('type', ['email', 'mobile']); // verification type
            $table->enum('purpose', ['registration', 'login', 'password_reset'])->default('login');
            $table->timestamp('expires_at');
            $table->boolean('is_verified')->default(false);
            $table->integer('attempts')->default(0);
            $table->timestamps();
            
            $table->index(['identifier', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
