<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Services\Auth\AuthService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        if (!$this->authService->isRegistrationAllowed()) {
            return redirect('/login')
                ->with('error', 'Registration is currently disabled.');
        }

        $authConfig = $this->authService->getConfig();
        
        return view('User::auth.register', [
            'authConfig' => $authConfig,
        ]);
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        if (!$this->authService->isRegistrationAllowed()) {
            return redirect('/login')
                ->with('error', 'Registration is currently disabled.');
        }

        $data = $request->all();

        $result = $this->authService->register($data);

        if ($result['success']) {
            // If OTP was sent (mobile registration)
            if (isset($result['otp_sent']) && $result['otp_sent']) {
                return back()->with([
                    'otp_sent' => true,
                    'mobile' => $request->mobile,
                    'expires_at' => $result['expires_at'],
                ])->with('info', $result['message']);
            }

            // If email verification is required
            if (isset($result['requires_verification']) && $result['requires_verification']) {
                return redirect('/login')
                    ->with('success', $result['message']);
            }

            // User is logged in automatically
            return redirect()->intended('/dashboard')
                ->with('success', $result['message']);
        }

        return back()->withErrors([
            'error' => $result['message'],
        ])->withInput($request->except(['password', 'password_confirmation', 'otp']));
    }
}
