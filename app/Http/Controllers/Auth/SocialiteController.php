<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialiteService;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Create a new SocialiteController.
     */
    public function __construct(
        private readonly SocialiteService $socialiteService,
    ) {}

    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the Google OAuth callback.
     */
    public function callback()
    {
        try {
            $result = $this->socialiteService->handleCallback();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Google authentication failed. Please try again.');
        }

        session()->regenerate();

        return redirect()->route('dashboard');
    }
}
