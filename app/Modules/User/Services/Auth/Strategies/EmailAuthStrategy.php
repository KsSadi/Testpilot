<?php

namespace App\Modules\User\Services\Auth\Strategies;

use App\Modules\User\Models\User;
use App\Modules\User\Models\AuthSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmailAuthStrategy implements AuthStrategyInterface
{
    /**
     * Attempt to authenticate with email and password.
     */
    public function attempt(array $credentials): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'Email login is currently disabled.',
            ];
        }

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Invalid credentials format.',
                'errors' => $validator->errors(),
            ];
        }

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid email or password.',
            ];
        }

        if (!$user->isActive()) {
            return [
                'success' => false,
                'message' => 'Your account has been suspended. Please contact support.',
            ];
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'Invalid email or password.',
            ];
        }

        // Check if email verification is required
        if (AuthSetting::getBool('email_verification_required') && !$user->hasVerifiedEmail()) {
            return [
                'success' => false,
                'message' => 'Please verify your email address before logging in.',
                'requires_verification' => true,
                'user_id' => $user->id,
            ];
        }

        // Remember me functionality
        $remember = $credentials['remember'] ?? false;

        Auth::login($user, $remember);

        return [
            'success' => true,
            'message' => 'Login successful.',
            'user' => $user,
        ];
    }

    /**
     * Register a new user with email.
     */
    public function register(array $data): array
    {
        if (!AuthSetting::getBool('email_registration_enabled', true)) {
            return [
                'success' => false,
                'message' => 'Email registration is currently disabled.',
            ];
        }

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ];
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => AuthSetting::get('default_user_role', 'user'),
            'status' => 'active',
        ]);

        // Check if email verification is required
        $requiresVerification = AuthSetting::getBool('email_verification_required');

        if (!$requiresVerification) {
            $user->update(['email_verified_at' => now()]);
            Auth::login($user);
        }

        return [
            'success' => true,
            'message' => $requiresVerification 
                ? 'Registration successful! Please verify your email address.' 
                : 'Registration successful! You are now logged in.',
            'user' => $user,
            'requires_verification' => $requiresVerification,
        ];
    }

    /**
     * Check if email authentication is enabled.
     */
    public function isEnabled(): bool
    {
        return AuthSetting::getBool('email_login_enabled', true);
    }
}
