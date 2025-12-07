<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Subsription\Models\UserSubscription;
use App\Modules\User\Models\User;
use Carbon\Carbon;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired subscriptions and update their status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        // Find all active subscriptions that have passed their end date
        $expiredSubscriptions = UserSubscription::where('status', 'active')
            ->whereNotNull('current_period_end')
            ->where('current_period_end', '<', Carbon::now())
            ->where('auto_renew', false) // Only expire non-auto-renewing subscriptions
            ->get();

        $count = $expiredSubscriptions->count();

        if ($count === 0) {
            $this->info('No expired subscriptions found.');
            return 0;
        }

        foreach ($expiredSubscriptions as $subscription) {
            // Update subscription status to expired
            $subscription->update(['status' => 'expired']);

            // Reset user's current subscription to free plan
            $freePlan = \App\Modules\Subsription\Models\SubscriptionPlan::where('slug', 'free')->first();
            
            if ($freePlan && $subscription->user) {
                $subscription->user->update([
                    'current_subscription_id' => null,
                    'current_plan_id' => $freePlan->id,
                ]);

                $this->line("Expired subscription #{$subscription->id} for user: {$subscription->user->name}");
            }
        }

        $this->info("Successfully expired {$count} subscription(s) and reverted users to free plan.");

        return 0;
    }
}
