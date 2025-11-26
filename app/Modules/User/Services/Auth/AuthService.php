<?php

namespace App\Modules\User\Services\Auth;

use App\Modules\User\Models\AuthSetting;
use App\Modules\User\Services\Auth\Strategies\EmailAuthStrategy;
use App\Modules\User\Services\Auth\Strategies\MobileAuthStrategy;
use App\Modules\User\Services\Auth\Strategies\SocialAuthStrategy;

class AuthService
{
    protected $emailStrategy;
    protected $mobileStrategy;
    protected $socialStrategy;

    public function __construct()
    {
        $this->emailStrategy = new EmailAuthStrategy();
        $this->mobileStrategy = new MobileAuthStrategy();
        $this->socialStrategy = new SocialAuthStrategy();
    }

    /**
     * Attempt login with appropriate strategy based on credentials.
     */
    public function login(array $credentials): array
    {
        // Determine which strategy to use based on provided credentials
        if (isset($credentials['email'])) {
            return $this->emailStrategy->attempt($credentials);
        }

        if (isset($credentials['mobile'])) {
            return $this->mobileStrategy->attempt($credentials);
        }

        return [
            'success' => false,
            'message' => 'Invalid credentials provided.',
        ];
    }

    /**
     * Register a new user with appropriate strategy.
     */
    public function register(array $data): array
    {
        // Determine which strategy to use based on provided data
        if (isset($data['email']) && !isset($data['mobile'])) {
            return $this->emailStrategy->register($data);
        }

        if (isset($data['mobile'])) {
            return $this->mobileStrategy->register($data);
        }

        return [
            'success' => false,
            'message' => 'Invalid registration data provided.',
        ];
    }

    /**
     * Get email authentication strategy.
     */
    public function email(): EmailAuthStrategy
    {
        return $this->emailStrategy;
    }

    /**
     * Get mobile authentication strategy.
     */
    public function mobile(): MobileAuthStrategy
    {
        return $this->mobileStrategy;
    }

    /**
     * Get social authentication strategy.
     */
    public function social(): SocialAuthStrategy
    {
        return $this->socialStrategy;
    }

    /**
     * Get enabled authentication methods.
     */
    public function getEnabledMethods(): array
    {
        $methods = [];

        if ($this->emailStrategy->isEnabled()) {
            $methods[] = 'email';
        }

        if ($this->mobileStrategy->isEnabled()) {
            $methods[] = 'mobile';
        }

        if ($this->socialStrategy->isEnabled()) {
            $methods['social'] = $this->socialStrategy->getEnabledProviders();
        }

        return $methods;
    }

    /**
     * Check if registration is allowed.
     */
    public function isRegistrationAllowed(): bool
    {
        return AuthSetting::getBool('allow_registration', true);
    }

    /**
     * Get authentication configuration for frontend.
     */
    public function getConfig(): array
    {
        return [
            'registration_allowed' => $this->isRegistrationAllowed(),
            'enabled_methods' => $this->getEnabledMethods(),
            'email_verification_required' => AuthSetting::getBool('email_verification_required'),
            'mobile_verification_required' => AuthSetting::getBool('mobile_verification_required'),
        ];
    }
}
