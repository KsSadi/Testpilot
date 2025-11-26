<?php

namespace App\Modules\User\Services\Auth;

use App\Modules\User\Models\OtpVerification;
use App\Modules\User\Models\AuthSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Generate and send OTP.
     */
    public function generate(string $identifier, string $type, string $purpose = 'login'): array
    {
        // Delete old OTPs for this identifier
        OtpVerification::where('identifier', $identifier)
            ->where('type', $type)
            ->where('purpose', $purpose)
            ->delete();

        // Get OTP settings
        $otpLength = (int) AuthSetting::get('otp_length', 6);
        $expiryMinutes = (int) AuthSetting::get('otp_expiry_minutes', 5);

        // Generate OTP
        $otp = $this->generateOtpCode($otpLength);

        // Store OTP
        $otpVerification = OtpVerification::create([
            'identifier' => $identifier,
            'otp' => $otp,
            'type' => $type,
            'purpose' => $purpose,
            'expires_at' => Carbon::now()->addMinutes($expiryMinutes),
            'is_verified' => false,
            'attempts' => 0,
        ]);

        return [
            'success' => true,
            'otp' => $otp,
            'expires_at' => $otpVerification->expires_at,
            'message' => "OTP sent to {$identifier}",
        ];
    }

    /**
     * Verify OTP.
     */
    public function verify(string $identifier, string $otp, string $type, string $purpose = 'login'): array
    {
        $otpVerification = OtpVerification::where('identifier', $identifier)
            ->where('type', $type)
            ->where('purpose', $purpose)
            ->where('is_verified', false)
            ->latest()
            ->first();

        if (!$otpVerification) {
            return [
                'success' => false,
                'message' => 'OTP not found or already verified.',
            ];
        }

        if ($otpVerification->isExpired()) {
            return [
                'success' => false,
                'message' => 'OTP has expired. Please request a new one.',
            ];
        }

        if ($otpVerification->attempts >= 3) {
            return [
                'success' => false,
                'message' => 'Too many attempts. Please request a new OTP.',
            ];
        }

        if ($otpVerification->otp !== $otp) {
            $otpVerification->incrementAttempts();
            return [
                'success' => false,
                'message' => 'Invalid OTP. Please try again.',
                'attempts_left' => 3 - $otpVerification->attempts,
            ];
        }

        // Mark as verified
        $otpVerification->markAsVerified();

        return [
            'success' => true,
            'message' => 'OTP verified successfully.',
        ];
    }

    /**
     * Check if can resend OTP (cooldown).
     */
    public function canResend(string $identifier, string $type, string $purpose = 'login'): bool
    {
        $cooldownSeconds = (int) AuthSetting::get('otp_resend_cooldown_seconds', 60);

        $lastOtp = OtpVerification::where('identifier', $identifier)
            ->where('type', $type)
            ->where('purpose', $purpose)
            ->latest()
            ->first();

        if (!$lastOtp) {
            return true;
        }

        return Carbon::now()->diffInSeconds($lastOtp->created_at) >= $cooldownSeconds;
    }

    /**
     * Generate OTP code.
     */
    protected function generateOtpCode(int $length = 6): string
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        
        return (string) random_int($min, $max);
    }

    /**
     * Clean up expired OTPs.
     */
    public function cleanupExpired(): int
    {
        return OtpVerification::where('expires_at', '<', Carbon::now())
            ->orWhere('is_verified', true)
            ->delete();
    }
}
