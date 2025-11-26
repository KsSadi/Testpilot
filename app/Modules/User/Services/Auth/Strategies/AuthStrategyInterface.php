<?php

namespace App\Modules\User\Services\Auth\Strategies;

interface AuthStrategyInterface
{
    /**
     * Attempt to authenticate with the given credentials.
     */
    public function attempt(array $credentials): array;

    /**
     * Register a new user with the given data.
     */
    public function register(array $data): array;

    /**
     * Check if this authentication method is enabled.
     */
    public function isEnabled(): bool;
}
