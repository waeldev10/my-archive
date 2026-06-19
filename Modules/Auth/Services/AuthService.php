<?php

declare(strict_types=1);

namespace Modules\Auth\Services;

use Modules\Auth\Actions\CreateUserAction;
use Modules\Auth\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

/**
 * Service layer for authentication operations.
 *
 * Handles registration, login, Google OAuth,
 * password resets, and email verification flows.
 */
class AuthService
{
    /**
     * Create a new user and issue a Sanctum token.
     *
     * @return array{user: User, token: string}
     */
    public function register(array $data): array
    {
        $action = app(CreateUserAction::class);
        $user = $action->execute($data['name'], $data['email'], $data['password']);

        event(new Registered($user));

        $token = $user->createToken('auth-token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Attempt login.
     *
     * @return array{user: User, token: string}|null  Null if credentials are invalid.
     */
    public function login(array $credentials): ?array
    {
        if (! Auth::attempt($credentials)) {
            return null;
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Authenticate or register a user via Google OAuth.
     *
     * @return array{user: User, token: string, is_new: bool}
     */
    public function googleLogin(string $googleId, string $email, string $name, string $avatar): array
    {
        $isNew = false;

        // Check if user exists by google_id
        $user = User::where('google_id', $googleId)->first();

        if (! $user) {
            // Check if user exists by email
            $user = User::where('email', $email)->first();

            if ($user) {
                // Link Google account to existing user
                $user->update(['google_id' => $googleId, 'avatar' => $avatar]);
            } else {
                // Create new user
                $action = app(CreateUserAction::class);
                $user = $action->fromGoogle($name, $email, $googleId, $avatar);
                $isNew = true;
            }
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return ['user' => $user, 'token' => $token, 'is_new' => $isNew];
    }

    /**
     * Revoke the current Sanctum token.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Send a password reset link.
     */
    public function sendPasswordResetLink(string $email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }

    /**
     * Reset the user's password using a reset token.
     */
    public function resetPassword(array $data): string
    {
        return Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );
    }

    /**
     * Resend the email verification notification.
     */
    public function resendVerification(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();
    }
}
