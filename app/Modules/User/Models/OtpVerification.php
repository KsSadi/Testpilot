<?php

namespace App\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OtpVerification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'identifier',
        'otp',
        'type',
        'purpose',
        'expires_at',
        'is_verified',
        'attempts',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
        'attempts' => 'integer',
    ];

    /**
     * Check if OTP is expired.
     */
    public function isExpired(): bool
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * Check if OTP is still valid.
     */
    public function isValid(): bool
    {
        return !$this->is_verified && !$this->isExpired() && $this->attempts < 3;
    }

    /**
     * Increment verification attempts.
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    /**
     * Mark OTP as verified.
     */
    public function markAsVerified(): void
    {
        $this->update(['is_verified' => true]);
    }
}
