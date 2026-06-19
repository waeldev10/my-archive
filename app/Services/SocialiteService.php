<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\CreateUserAction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

/**
 * Service for handling Google OAuth authentication.
 *
 * This service token is used by both the web SocialiteController
 * (browser-based OAuth flow) and the API AuthController
 * (mobile/app-based OAuth flow).
 */
class SocialiteService
{
    /**
     * Handle the Google OAuth callback from the web flow.
     *
     * This is used by the Livewire/web Socialite redirect+c flow.
     *
     * @return array{user: User, is_new: bool}
     */
    public function handleCallback(): array
    {
        $googleUser = Socialite::driver('google')->user();

        return $this->findOrCreateUser(
            $googleUser->getId(),
            $googleUser->getEmail(),
            $googleUser->getName(),
            $googleUser->getAvatar()
        );
    }

    /**
     * Handle a Google OAuth credential from the API flow.
     *
     * This is used by the API endpoint for mobile/SPA clients.
     *
     * @return array{user: User, is_new: bool}
     */
    public function handleGoogleToken(string $googleToken): array
    {
        $googleUser = Socialite::driver('google')->stateless()->userFromToken($googleToken);

        return $this->findOrCreateUser(
            $googleUser->getId(),
            $googleUser->getEmail(),
            $googleUser->getName(),
            $googleUser->getAvatar()
        );
    }

    /**
     * Find or create a user from Google OAuth data.
     *
     * @return array{user: User, is_new: bool}
     */
    private function findOrCreateUser(string $googleId, string $email, string $name, string $avatar): array
    {
        $isNew = false;

        $user = User::where('google_id', $googleId)->first();

        if (! $user) {
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update(['google_id' => $googleId, 'avatar' => $avatar]);
            } else {
                $action = app(CreateUserAction::class);
                $user = $action->fromGoogle($name, $email, $googleId, $avatar);
                $isNew = true;
            }
        }

        Auth::login($user);

        return ['user' => $user, 'is_new' => $isNew];
    }
}
