<?php

namespace App\Modules\Subsription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'yearly_price',
        'yearly_discount_percentage',
        'max_projects',
        'max_modules',
        'max_test_cases',
        'max_collaborators',
        'features',
        'is_active',
        'is_featured',
        'sort_order',
        'stripe_monthly_price_id',
        'stripe_yearly_price_id',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class, 'subscription_plan_id');
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->subscriptions()->where('status', 'active');
    }

    // Helper methods
    public function isUnlimitedProjects(): bool
    {
        return $this->max_projects === -1;
    }

    public function isUnlimitedModules(): bool
    {
        return $this->max_modules === -1;
    }

    public function isUnlimitedTestCases(): bool
    {
        return $this->max_test_cases === -1;
    }

    public function isUnlimitedCollaborators(): bool
    {
        return $this->max_collaborators === -1;
    }

    public function getCalculatedYearlyPriceAttribute(): float
    {
        if ($this->yearly_price > 0) {
            return $this->yearly_price;
        }
        
        // Calculate yearly price from monthly with discount
        $yearlyFromMonthly = $this->monthly_price * 12;
        $discount = ($yearlyFromMonthly * $this->yearly_discount_percentage) / 100;
        
        return $yearlyFromMonthly - $discount;
    }

    public function getYearlySavingsAttribute(): float
    {
        $monthlyTotal = $this->monthly_price * 12;
        return $monthlyTotal - $this->calculated_yearly_price;
    }

    public function getYearlyPrice(): float
    {
        return $this->yearly_price ?? $this->calculated_yearly_price;
    }

    public function getMonthlyPrice(): float
    {
        return $this->monthly_price;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('monthly_price');
    }
}
