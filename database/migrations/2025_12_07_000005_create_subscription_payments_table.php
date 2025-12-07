<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained('user_subscriptions')->onDelete('cascade');
            
            // Payment details
            $table->enum('payment_method', ['stripe', 'bkash', 'rocket', 'nagad', 'bank_transfer', 'other'])->default('stripe');
            $table->string('transaction_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            
            // Amount
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            
            // Status
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            
            // Manual payment details
            $table->string('sender_number')->nullable(); // For bKash/Rocket
            $table->text('payment_proof')->nullable(); // File path for proof
            $table->text('admin_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('approved_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
