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
        // Step 1: Change column to VARCHAR to allow any value temporarily
        DB::statement("ALTER TABLE `user_subscriptions` MODIFY `payment_method` VARCHAR(50) DEFAULT 'stripe'");
        
        // Step 2: Update any existing 'manual' values to 'bank_transfer'
        DB::table('user_subscriptions')
            ->where('payment_method', 'manual')
            ->update(['payment_method' => 'bank_transfer']);
        
        // Step 3: Convert back to ENUM with new payment methods
        DB::statement("ALTER TABLE `user_subscriptions` MODIFY `payment_method` ENUM('stripe', 'bkash', 'nagad', 'rocket', 'bank_transfer') DEFAULT 'stripe'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE `user_subscriptions` MODIFY `payment_method` ENUM('stripe', 'manual') DEFAULT 'stripe'");
    }
};
