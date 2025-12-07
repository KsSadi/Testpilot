<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_subscription_id')->nullable()->after('remember_token')->constrained('user_subscriptions')->nullOnDelete();
            
            // Quick access fields (denormalized for performance)
            $table->foreignId('current_plan_id')->nullable()->after('current_subscription_id')->constrained('subscription_plans')->nullOnDelete();
            
            // Override limits (for special cases, -1 means use plan limits)
            $table->integer('override_max_projects')->default(-1)->after('current_plan_id');
            $table->integer('override_max_modules')->default(-1);
            $table->integer('override_max_test_cases')->default(-1);
            $table->integer('override_max_collaborators')->default(-1);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_subscription_id']);
            $table->dropForeign(['current_plan_id']);
            $table->dropColumn([
                'current_subscription_id',
                'current_plan_id',
                'override_max_projects',
                'override_max_modules',
                'override_max_test_cases',
                'override_max_collaborators',
            ]);
        });
    }
};
