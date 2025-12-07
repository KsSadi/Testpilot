<?php

namespace App\Modules\Subsription\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'payment_method',
        'transaction_id',
        'stripe_payment_intent_id',
        'amount',
        'currency',
        'status',
        'sender_number',
        'payment_proof',
        'admin_notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approve(User $admin, string $notes = null)
    {
        $this->update([
            'status' => 'completed',
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'admin_notes' => $notes,
        ]);

        // Activate the subscription
        if ($this->subscription) {
            $this->subscription->update(['status' => 'active']);
            
            // Update user's current subscription
            $this->user->update([
                'current_subscription_id' => $this->subscription->id,
                'current_plan_id' => $this->subscription->subscription_plan_id,
            ]);
        }
    }

    public function reject(User $admin, string $reason)
    {
        $this->update([
            'status' => 'failed',
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'admin_notes' => $reason,
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeManual($query)
    {
        return $query->whereIn('payment_method', ['bkash', 'rocket', 'nagad', 'bank_transfer', 'other']);
    }
}
