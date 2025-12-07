<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Starter, Pro, Business
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Pricing
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->decimal('yearly_price', 10, 2)->default(0);
            $table->integer('yearly_discount_percentage')->default(20); // Dynamic discount %
            
            // Limits
            $table->integer('max_projects')->default(3); // -1 for unlimited
            $table->integer('max_modules')->default(1); // -1 for unlimited
            $table->integer('max_test_cases')->default(10); // -1 for unlimited
            $table->integer('max_collaborators')->default(1); // -1 for unlimited
            
            // Features (JSON for flexibility)
            $table->json('features')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            
            // Stripe IDs
            $table->string('stripe_monthly_price_id')->nullable();
            $table->string('stripe_yearly_price_id')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
