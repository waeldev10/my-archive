<?php

declare(strict_types=1);

namespace Modules\Auth\Actions;

use Modules\Auth\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Single-responsibility action for creating a new user.
 *
 * Handles both email/password registration and OAuth account creation,
 * ensuring consistent user setup across the application.
 */
class CreateUserAction
{
    /**
     * Create a new user with email and password.
     */
    public function execute(string $name, string $email, string $password): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }

    /**
     * Create a new user from Google OAuth data.
     */
    public function fromGoogle(string $name, string $email, string $googleId, string $avatar): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'google_id' => $googleId,
            'avatar' => $avatar,
            'password' => Hash::make(Str::random(32)),
        ]);
    }
}
