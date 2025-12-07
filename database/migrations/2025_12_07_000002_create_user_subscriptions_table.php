<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            
            // Subscription details
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->enum('status', ['active', 'cancelled', 'expired', 'pending', 'suspended'])->default('pending');
            
            // Payment info
            $table->enum('payment_method', ['stripe', 'manual'])->default('stripe');
            $table->string('stripe_subscription_id')->nullable();
            $table->string('transaction_id')->nullable(); // For manual payments
            
            // Billing periods
            $table->datetime('current_period_start')->nullable();
            $table->datetime('current_period_end')->nullable();
            $table->datetime('trial_ends_at')->nullable();
            $table->datetime('cancelled_at')->nullable();
            
            // Pricing (store at time of subscription for historical records)
            $table->decimal('amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2);
            
            // Coupon (will add constraint later)
            $table->unsignedBigInteger('coupon_id')->nullable();
            
            // Flags
            $table->boolean('cancel_at_period_end')->default(false);
            $table->boolean('auto_renew')->default(true);
            
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
