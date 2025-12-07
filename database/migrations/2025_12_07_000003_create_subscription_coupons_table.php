<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Discount type
            $table->enum('discount_type', ['percentage', 'fixed_amount'])->default('percentage');
            $table->decimal('discount_value', 10, 2); // Percentage or fixed amount
            
            // Applicability
            $table->json('applicable_plan_ids')->nullable(); // null = all plans
            $table->boolean('applies_to_yearly_only')->default(false);
            
            // Duration
            $table->enum('duration', ['once', 'forever', 'repeating'])->default('once');
            $table->integer('duration_in_months')->nullable(); // For repeating
            
            // Validity
            $table->datetime('valid_from')->nullable();
            $table->datetime('valid_until')->nullable();
            
            // Usage limits
            $table->integer('max_redemptions')->nullable(); // null = unlimited
            $table->integer('max_redemptions_per_user')->default(1);
            $table->integer('times_redeemed')->default(0);
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_coupons');
    }
};
