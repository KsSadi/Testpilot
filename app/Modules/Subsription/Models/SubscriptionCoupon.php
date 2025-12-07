<?php

namespace App\Modules\Subsription\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SubscriptionCoupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'applicable_plan_ids',
        'applies_to_yearly_only',
        'duration',
        'duration_in_months',
        'valid_from',
        'valid_until',
        'max_redemptions',
        'max_redemptions_per_user',
        'times_redeemed',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'applicable_plan_ids' => 'array',
        'applies_to_yearly_only' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($coupon) {
            if (empty($coupon->code)) {
                $coupon->code = strtoupper(Str::random(8));
            }
        });
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class, 'coupon_id');
    }

    // Validation methods
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->valid_from && now()->isBefore($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && now()->isAfter($this->valid_until)) {
            return false;
        }

        if ($this->max_redemptions && $this->times_redeemed >= $this->max_redemptions) {
            return false;
        }

        return true;
    }

    public function canBeUsedBy(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $userRedemptions = $this->redemptions()->where('user_id', $user->id)->count();
        
        return $userRedemptions < $this->max_redemptions_per_user;
    }

    public function appliesTo($planId, $billingCycle): bool
    {
        if ($this->applies_to_yearly_only && $billingCycle !== 'yearly') {
            return false;
        }

        if ($this->applicable_plan_ids === null) {
            return true; // Applies to all plans
        }

        return in_array($planId, $this->applicable_plan_ids);
    }

    public function calculateDiscount($amount): float
    {
        if ($this->discount_type === 'percentage') {
            return ($amount * $this->discount_value) / 100;
        }

        return min($this->discount_value, $amount);
    }

    public function redeem(User $user, UserSubscription $subscription = null): CouponRedemption
    {
        $this->increment('times_redeemed');

        return CouponRedemption::create([
            'coupon_id' => $this->id,
            'user_id' => $user->id,
            'subscription_id' => $subscription?->id,
            'discount_amount' => $this->calculateDiscount($subscription?->amount ?? 0),
            'redeemed_at' => now(),
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now());
            });
    }

    public function scopeAvailable($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('max_redemptions')
                  ->orWhereRaw('times_redeemed < max_redemptions');
            });
    }
}
