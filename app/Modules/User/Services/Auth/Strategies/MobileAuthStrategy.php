<?php

namespace App\Modules\User\Services\Auth\Strategies;

use App\Modules\User\Models\User;
use App\Modules\User\Models\AuthSetting;
use App\Modules\User\Services\Auth\OtpService;
use App\Modules\User\Services\Auth\SmsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MobileAuthStrategy implements AuthStrategyInterface
{
    protected $otpService;
    protected $smsService;

    public function __construct()
    {
        $this->otpService = new OtpService();
        $this->smsService = new SmsService();
    }

    /**
     * Attempt to authenticate with mobile and OTP.
     */
    public function attempt(array $credentials): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'Mobile login is currently disabled.',
            ];
        }

        $validator = Validator::make($credentials, [
            'mobile' => 'required|string',
            'otp' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Invalid credentials format.',
                'errors' => $validator->errors(),
            ];
        }

        // If OTP is not provided, send OTP
        if (!isset($credentials['otp'])) {
            return $this->sendLoginOtp($credentials['mobile']);
        }

        // Verify OTP
        $otpResult = $this->otpService->verify(
            $credentials['mobile'],
            $credentials['otp'],
            'mobile',
            'login'
        );

        if (!$otpResult['success']) {
            return $otpResult;
        }

        // Find user by mobile
        $user = User::where('mobile', $credentials['mobile'])->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'No account found with this mobile number.',
            ];
        }

        if (!$user->isActive()) {
            return [
                'success' => false,
                'message' => 'Your account has been suspended. Please contact support.',
            ];
        }

        Auth::login($user);

        return [
            'success' => true,
            'message' => 'Login successful.',
            'user' => $user,
        ];
    }

    /**
     * Register a new user with mobile.
     */
    public function register(array $data): array
    {
        if (!AuthSetting::getBool('mobile_registration_enabled')) {
            return [
                'success' => false,
                'message' => 'Mobile registration is currently disabled.',
            ];
        }

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|unique:users',
            'password' => 'sometimes|required|string|min:8|confirmed',
            'otp' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ];
        }

        // If OTP is not provided, send OTP
        if (!isset($data['otp'])) {
            return $this->sendRegistrationOtp($data['mobile']);
        }

        // Verify OTP
        $otpResult = $this->otpService->verify(
            $data['mobile'],
            $data['otp'],
            'mobile',
            'registration'
        );

        if (!$otpResult['success']) {
            return $otpResult;
        }

        // Create user
        $userData = [
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'role' => AuthSetting::get('default_user_role', 'user'),
            'status' => 'active',
            'mobile_verified_at' => now(),
        ];

        // Password is optional for mobile registration
        if (isset($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        $user = User::create($userData);

        Auth::login($user);

        return [
            'success' => true,
            'message' => 'Registration successful! You are now logged in.',
            'user' => $user,
        ];
    }

    /**
     * Send login OTP to mobile.
     */
    protected function sendLoginOtp(string $mobile): array
    {
        if (!$this->smsService->isValidMobile($mobile)) {
            return [
                'success' => false,
                'message' => 'Invalid mobile number format.',
            ];
        }

        // Check if user exists
        $user = User::where('mobile', $mobile)->first();
        if (!$user) {
            return [
                'success' => false,
                'message' => 'No account found with this mobile number.',
            ];
        }

        // Check cooldown
        if (!$this->otpService->canResend($mobile, 'mobile', 'login')) {
            $cooldown = (int) AuthSetting::get('otp_resend_cooldown_seconds', 60);
            return [
                'success' => false,
                'message' => "Please wait {$cooldown} seconds before requesting a new OTP.",
            ];
        }

        // Generate and send OTP
        $otpResult = $this->otpService->generate($mobile, 'mobile', 'login');
        $smsResult = $this->smsService->sendOtp($mobile, $otpResult['otp']);

        if (!$smsResult['success']) {
            return [
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.',
            ];
        }

        return [
            'success' => true,
            'message' => 'OTP sent to your mobile number.',
            'otp_sent' => true,
            'expires_at' => $otpResult['expires_at'],
        ];
    }

    /**
     * Send registration OTP to mobile.
     */
    protected function sendRegistrationOtp(string $mobile): array
    {
        if (!$this->smsService->isValidMobile($mobile)) {
            return [
                'success' => false,
                'message' => 'Invalid mobile number format.',
            ];
        }

        // Check cooldown
        if (!$this->otpService->canResend($mobile, 'mobile', 'registration')) {
            $cooldown = (int) AuthSetting::get('otp_resend_cooldown_seconds', 60);
            return [
                'success' => false,
                'message' => "Please wait {$cooldown} seconds before requesting a new OTP.",
            ];
        }

        // Generate and send OTP
        $otpResult = $this->otpService->generate($mobile, 'mobile', 'registration');
        $smsResult = $this->smsService->sendOtp($mobile, $otpResult['otp']);

        if (!$smsResult['success']) {
            return [
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.',
            ];
        }

        return [
            'success' => true,
            'message' => 'OTP sent to your mobile number.',
            'otp_sent' => true,
            'expires_at' => $otpResult['expires_at'],
        ];
    }

    /**
     * Check if mobile authentication is enabled.
     */
    public function isEnabled(): bool
    {
        return AuthSetting::getBool('mobile_login_enabled');
    }
}
