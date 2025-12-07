<?php

namespace App\Modules\User\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Modules\Subsription\Traits\HasSubscription;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasSubscription;

    /**
     * The guard name for Spatie Permission.
     *
     * @var string
     */
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'phone',
        'password',
        'status',
        'avatar',
        'address',
        'date_of_birth',
        'email_verified_at',
        'mobile_verified_at',
        'current_subscription_id',
        'current_plan_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=06b6d4&color=fff';
    }

    /**
     * Get the auth providers for the user.
     */
    public function authProviders()
    {
        return $this->hasMany(AuthProvider::class);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if email is verified.
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Check if mobile is verified.
     */
    public function hasVerifiedMobile(): bool
    {
        return !is_null($this->mobile_verified_at);
    }

    /**
     * Get the user's current subscription.
     */
    public function currentSubscription()
    {
        return $this->belongsTo(\App\Modules\Subsription\Models\UserSubscription::class, 'current_subscription_id');
    }

    /**
     * Get all subscriptions for the user.
     */
    public function subscriptions()
    {
        return $this->hasMany(\App\Modules\Subsription\Models\UserSubscription::class);
    }

    /**
     * Get the user's current plan.
     */
    public function currentPlan()
    {
        return $this->belongsTo(\App\Modules\Subsription\Models\SubscriptionPlan::class, 'current_plan_id');
    }

    /**
     * Get AI usage logs for the user.
     */
    public function aiUsageLogs()
    {
        return $this->hasMany(\App\Models\AIUsageLog::class);
    }

    /**
     * Get the user's projects.
     */
    public function projects()
    {
        return $this->hasMany(\App\Modules\Cypress\Models\Project::class, 'created_by');
    }

    /**
     * Get all usage statistics for the user.
     */
    public function getAllUsageStats()
    {
        $now = now();
        $currentPeriodStart = $this->currentSubscription?->current_period_start ?? $now->copy()->startOfMonth();
        
        // Get AI usage stats for current billing period
        $aiUsage = $this->aiUsageLogs()
            ->where('created_at', '>=', $currentPeriodStart)
            ->selectRaw('
                COUNT(*) as total_requests,
                SUM(total_tokens) as total_tokens,
                SUM(estimated_cost) as total_cost
            ')
            ->first();

        // Get plan limits
        $plan = $this->currentPlan;
        $limits = $plan ? [
            'ai_requests' => $plan->features['ai_requests'] ?? 0,
            'ai_tokens' => $plan->features['ai_tokens'] ?? 0,
        ] : [
            'ai_requests' => 0,
            'ai_tokens' => 0,
        ];

        // Get project/module/test case counts
        $projectsCount = $this->projects()->count();
        $modulesCount = \DB::table('modules')->where('created_by', $this->id)->count();
        $testCasesCount = \DB::table('test_cases')->where('created_by', $this->id)->count();
        $sharedCount = \DB::table('project_shares')->where('shared_with_user_id', $this->id)->count();

        return [
            'projects_count' => $projectsCount,
            'modules_count' => $modulesCount,
            'test_cases_count' => $testCasesCount,
            'shared_count' => $sharedCount,
            'ai_requests' => [
                'used' => $aiUsage->total_requests ?? 0,
                'limit' => $limits['ai_requests'],
                'percentage' => $limits['ai_requests'] > 0 
                    ? min(100, (($aiUsage->total_requests ?? 0) / $limits['ai_requests']) * 100)
                    : 0,
            ],
            'ai_tokens' => [
                'used' => $aiUsage->total_tokens ?? 0,
                'limit' => $limits['ai_tokens'],
                'percentage' => $limits['ai_tokens'] > 0 
                    ? min(100, (($aiUsage->total_tokens ?? 0) / $limits['ai_tokens']) * 100)
                    : 0,
            ],
            'total_cost' => $aiUsage->total_cost ?? 0,
            'period_start' => $currentPeriodStart,
            'period_end' => $this->currentSubscription?->current_period_end ?? $now->copy()->endOfMonth(),
        ];
    }
}
