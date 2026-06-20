<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Controllers\Web;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function login(): View
    {
        return view('auth::pages.login');
    }

    /**
     * Show the registration form.
     */
    public function register(): View
    {
        return view('auth::pages.register');
    }

    /**
     * Show the forgot password form.
     */
    public function forgotPassword(): View
    {
        return view('auth::pages.forgot-password');
    }

    /**
     * Show the password reset form.
     */
    public function resetPassword(string $token): View
    {
        return view('auth::pages.reset-password', [
            'token' => $token,
        ]);
    }

    /**
     * Log the user out and invalidate the session.
     */
    public function logout(Request $request): RedirectResponse
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Verify the user's email address.
     */
    public function verifyEmail(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->route('dashboard');
    }

    /**
     * Resend the email verification notification.
     */
    public function resendVerification(Request $request): RedirectResponse
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent!');
    }
}
