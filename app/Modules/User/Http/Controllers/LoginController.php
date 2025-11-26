<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        $authConfig = $this->authService->getConfig();
        
        return view('User::auth.login', [
            'authConfig' => $authConfig,
        ]);
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'mobile', 'password', 'otp', 'remember']);

        $result = $this->authService->login($credentials);

        if ($result['success']) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard')
                ->with('success', $result['message']);
        }

        // If OTP was sent (mobile login)
        if (isset($result['otp_sent']) && $result['otp_sent']) {
            return back()->with([
                'otp_sent' => true,
                'mobile' => $request->mobile,
                'expires_at' => $result['expires_at'],
            ])->with('info', $result['message']);
        }

        return back()->withErrors([
            'error' => $result['message'],
        ])->withInput($request->except(['password', 'otp']));
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Redirect to social provider.
     */
    public function socialRedirect(string $provider)
    {
        return $this->authService->social()->redirect($provider);
    }

    /**
     * Handle social provider callback.
     */
    public function socialCallback(string $provider)
    {
        $result = $this->authService->social()->handleCallback($provider);

        if ($result['success']) {
            return redirect()->intended('/dashboard')
                ->with('success', $result['message']);
        }

        return redirect('/login')
            ->withErrors(['error' => $result['message']]);
    }
}
