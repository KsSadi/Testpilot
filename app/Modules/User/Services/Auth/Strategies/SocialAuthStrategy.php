<?php

namespace App\Modules\User\Services\Auth\Strategies;

use App\Modules\User\Models\User;
use App\Modules\User\Models\AuthProvider;
use App\Modules\User\Models\AuthSetting;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthStrategy implements AuthStrategyInterface
{
    /**
     * Attempt to authenticate via social provider.
     * Note: This method is not used for social auth as it's handled via OAuth flow.
     */
    public function attempt(array $credentials): array
    {
        return [
            'success' => false,
            'message' => 'Direct authentication not supported for social login. Use OAuth flow.',
        ];
    }

    /**
     * Handle social authentication callback.
     */
    public function handleCallback(string $provider): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'Social login is currently disabled.',
            ];
        }

        if (!$this->isProviderEnabled($provider)) {
            return [
                'success' => false,
                'message' => ucfirst($provider) . ' login is currently disabled.',
            ];
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to authenticate with ' . ucfirst($provider) . '.',
                'error' => $e->getMessage(),
            ];
        }

        // Check if user already exists with this provider
        $authProvider = AuthProvider::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($authProvider) {
            $user = $authProvider->user;
            
            if (!$user->isActive()) {
                return [
                    'success' => false,
                    'message' => 'Your account has been suspended. Please contact support.',
                ];
            }

            // Update provider tokens
            $authProvider->update([
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken ?? null,
            ]);

            Auth::login($user);

            return [
                'success' => true,
                'message' => 'Login successful.',
                'user' => $user,
            ];
        }

        // Check if user exists with the same email
        if ($socialUser->getEmail()) {
            $user = User::where('email', $socialUser->getEmail())->first();
            
            if ($user) {
                // Link social account to existing user
                AuthProvider::create([
                    'user_id' => $user->id,
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'provider_token' => $socialUser->token,
                    'provider_refresh_token' => $socialUser->refreshToken ?? null,
                ]);

                if (!$user->isActive()) {
                    return [
                        'success' => false,
                        'message' => 'Your account has been suspended. Please contact support.',
                    ];
                }

                Auth::login($user);

                return [
                    'success' => true,
                    'message' => 'Social account linked and login successful.',
                    'user' => $user,
                ];
            }
        }

        // Create new user
        return $this->register([
            'provider' => $provider,
            'social_user' => $socialUser,
        ]);
    }

    /**
     * Register a new user via social provider.
     */
    public function register(array $data): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'Social registration is currently disabled.',
            ];
        }

        $provider = $data['provider'];
        $socialUser = $data['social_user'];

        // Create user
        $user = User::create([
            'name' => $socialUser->getName() ?? 'User',
            'email' => $socialUser->getEmail(),
            'avatar' => $socialUser->getAvatar(),
            'role' => AuthSetting::get('default_user_role', 'user'),
            'status' => 'active',
            'email_verified_at' => now(), // Auto-verify social logins
        ]);

        // Create auth provider record
        AuthProvider::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'provider_token' => $socialUser->token,
            'provider_refresh_token' => $socialUser->refreshToken ?? null,
        ]);

        Auth::login($user);

        return [
            'success' => true,
            'message' => 'Registration successful! You are now logged in.',
            'user' => $user,
        ];
    }

    /**
     * Redirect to social provider.
     */
    public function redirect(string $provider): mixed
    {
        if (!$this->isEnabled()) {
            return redirect()->route('login')
                ->with('error', 'Social login is currently disabled.');
        }

        if (!$this->isProviderEnabled($provider)) {
            return redirect()->route('login')
                ->with('error', ucfirst($provider) . ' login is currently disabled.');
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Failed to redirect to ' . ucfirst($provider) . '.');
        }
    }

    /**
     * Check if social authentication is enabled.
     */
    public function isEnabled(): bool
    {
        return AuthSetting::getBool('social_login_enabled');
    }

    /**
     * Check if specific provider is enabled.
     */
    public function isProviderEnabled(string $provider): bool
    {
        $key = strtolower($provider) . '_login_enabled';
        return AuthSetting::getBool($key);
    }

    /**
     * Get enabled social providers.
     */
    public function getEnabledProviders(): array
    {
        if (!$this->isEnabled()) {
            return [];
        }

        $providers = AuthSetting::getJson('social_providers', ['google', 'facebook', 'github']);
        $enabled = [];

        foreach ($providers as $provider) {
            if ($this->isProviderEnabled($provider)) {
                $enabled[] = $provider;
            }
        }

        return $enabled;
    }
}
