<?php

namespace App\Modules\Subsription\Traits;

use App\Modules\Subsription\Models\SubscriptionPlan;
use App\Modules\Subsription\Models\UserSubscription;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasSubscription
{
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function currentSubscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class, 'current_subscription_id');
    }

    public function currentPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'current_plan_id');
    }

    // Check subscription status
    public function hasActiveSubscription(): bool
    {
        return $this->currentSubscription && $this->currentSubscription->isActive();
    }

    public function isOnFreePlan(): bool
    {
        return !$this->current_plan_id || $this->currentPlan->monthly_price == 0;
    }

    public function isOnPlan(string $slug): bool
    {
        return $this->currentPlan && $this->currentPlan->slug === $slug;
    }

    // Get limits
    public function getMaxProjects(): int
    {
        if ($this->override_max_projects !== -1) {
            return $this->override_max_projects;
        }

        return $this->currentPlan->max_projects ?? 3; // Default to free plan
    }

    public function getMaxModules(): int
    {
        if ($this->override_max_modules !== -1) {
            return $this->override_max_modules;
        }

        return $this->currentPlan->max_modules ?? 1;
    }

    public function getMaxTestCases(): int
    {
        if ($this->override_max_test_cases !== -1) {
            return $this->override_max_test_cases;
        }

        return $this->currentPlan->max_test_cases ?? 10;
    }

    public function getMaxCollaborators(): int
    {
        if ($this->override_max_collaborators !== -1) {
            return $this->override_max_collaborators;
        }

        return $this->currentPlan->max_collaborators ?? 1;
    }

    // Check if user can perform action
    public function canCreateProject(): bool
    {
        $limit = $this->getMaxProjects();
        
        if ($limit === -1) {
            return true; // Unlimited
        }

        $currentCount = $this->projects()->count();
        return $currentCount < $limit;
    }

    public function canCreateModule(): bool
    {
        $limit = $this->getMaxModules();
        
        if ($limit === -1) {
            return true;
        }

        $currentCount = \DB::table('modules')->where('created_by', $this->id)->count();
        return $currentCount < $limit;
    }

    public function canCreateTestCase(): bool
    {
        $limit = $this->getMaxTestCases();
        
        if ($limit === -1) {
            return true;
        }

        $currentCount = \DB::table('test_cases')->where('created_by', $this->id)->count();
        return $currentCount < $limit;
    }

    public function canShareWithMoreCollaborators(): bool
    {
        $limit = $this->getMaxCollaborators();
        
        if ($limit === -1) {
            return true;
        }

        // Count active shares (where user is the sharer)
        $currentCount = \DB::table('project_shares')
            ->join('projects', 'project_shares.shareable_id', '=', 'projects.id')
            ->where('project_shares.shareable_type', 'App\\Modules\\Cypress\\Models\\Project')
            ->where('projects.user_id', $this->id)
            ->distinct('project_shares.shared_with')
            ->count('project_shares.shared_with');

        return $currentCount < $limit;
    }

    // Get usage counts
    public function getProjectsUsage(): array
    {
        $limit = $this->getMaxProjects();
        $used = $this->projects()->count();
        
        return [
            'used' => $used,
            'limit' => $limit,
            'remaining' => $limit === -1 ? -1 : max(0, $limit - $used),
            'percentage' => $limit === -1 ? 0 : min(100, ($used / $limit) * 100),
            'is_unlimited' => $limit === -1,
        ];
    }

    public function getModulesUsage(): array
    {
        $limit = $this->getMaxModules();
        $used = \DB::table('modules')->where('created_by', $this->id)->count();
        
        return [
            'used' => $used,
            'limit' => $limit,
            'remaining' => $limit === -1 ? -1 : max(0, $limit - $used),
            'percentage' => $limit === -1 ? 0 : min(100, ($used / $limit) * 100),
            'is_unlimited' => $limit === -1,
        ];
    }

    public function getTestCasesUsage(): array
    {
        $limit = $this->getMaxTestCases();
        $used = \DB::table('test_cases')->where('created_by', $this->id)->count();
        
        return [
            'used' => $used,
            'limit' => $limit,
            'remaining' => $limit === -1 ? -1 : max(0, $limit - $used),
            'percentage' => $limit === -1 ? 0 : min(100, ($used / $limit) * 100),
            'is_unlimited' => $limit === -1,
        ];
    }

    public function getCollaboratorsUsage(): array
    {
        $limit = $this->getMaxCollaborators();
        $used = \DB::table('project_shares')
            ->join('projects', 'project_shares.shareable_id', '=', 'projects.id')
            ->where('project_shares.shareable_type', 'App\\Modules\\Cypress\\Models\\Project')
            ->where('projects.user_id', $this->id)
            ->distinct('project_shares.shared_with')
            ->count('project_shares.shared_with');
        
        return [
            'used' => $used,
            'limit' => $limit,
            'remaining' => $limit === -1 ? -1 : max(0, $limit - $used),
            'percentage' => $limit === -1 ? 0 : min(100, ($used / $limit) * 100),
            'is_unlimited' => $limit === -1,
        ];
    }

    public function getAllUsageStats(): array
    {
        return [
            'projects' => $this->getProjectsUsage(),
            'modules' => $this->getModulesUsage(),
            'test_cases' => $this->getTestCasesUsage(),
            'collaborators' => $this->getCollaboratorsUsage(),
        ];
    }
}
