<?php

namespace App\Modules\Subsription\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'billing_cycle',
        'status',
        'payment_method',
        'stripe_subscription_id',
        'transaction_id',
        'current_period_start',
        'current_period_end',
        'trial_ends_at',
        'cancelled_at',
        'amount',
        'discount_amount',
        'final_amount',
        'coupon_id',
        'cancel_at_period_end',
        'auto_renew',
    ];

    protected $casts = [
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'trial_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'cancel_at_period_end' => 'boolean',
        'auto_renew' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(SubscriptionCoupon::class, 'coupon_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class, 'subscription_id');
    }

    // Status checkers
    public function isActive(): bool
    {
        return $this->status === 'active' 
            && (!$this->current_period_end || $this->current_period_end->isFuture());
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' 
            || ($this->current_period_end && $this->current_period_end->isPast());
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    // Actions
    public function cancel($immediately = false)
    {
        if ($immediately) {
            $this->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);
            
            // Update user's current subscription
            $this->user->update(['current_subscription_id' => null, 'current_plan_id' => null]);
        } else {
            $this->update([
                'cancel_at_period_end' => true,
                'cancelled_at' => now(),
            ]);
        }
    }

    public function resume()
    {
        $this->update([
            'status' => 'active',
            'cancel_at_period_end' => false,
            'cancelled_at' => null,
        ]);
    }

    public function renew()
    {
        $newPeriodStart = $this->current_period_end ?? now();
        $newPeriodEnd = $this->billing_cycle === 'yearly' 
            ? $newPeriodStart->copy()->addYear() 
            : $newPeriodStart->copy()->addMonth();

        $this->update([
            'current_period_start' => $newPeriodStart,
            'current_period_end' => $newPeriodEnd,
            'status' => 'active',
        ]);
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->current_period_end) {
            return 0;
        }
        
        return max(0, now()->diffInDays($this->current_period_end, false));
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpiring($query, $days = 7)
    {
        return $query->where('status', 'active')
            ->whereBetween('current_period_end', [now(), now()->addDays($days)]);
    }
}
