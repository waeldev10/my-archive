<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case User = 'user';

    /**
     * Check if the role is admin.
     */
    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }
}
