<?php

declare(strict_types=1);

namespace Modules\Archives\Policies;

use Modules\Archives\Models\Archive;
use Modules\Auth\Models\User;

class ArchivePolicy
{
    /**
     * Determine if the user can view any archives.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the archive.
     */
    public function view(User $user, Archive $archive): bool
    {
        return $user->id === $archive->user_id;
    }

    /**
     * Determine if the user can create archives.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the archive.
     */
    public function update(User $user, Archive $archive): bool
    {
        return $user->id === $archive->user_id;
    }

    /**
     * Determine if the user can delete the archive.
     */
    public function delete(User $user, Archive $archive): bool
    {
        return $user->id === $archive->user_id;
    }

    /**
     * Determine if the user can restore the archive.
     */
    public function restore(User $user, Archive $archive): bool
    {
        return $user->id === $archive->user_id;
    }

    /**
     * Determine if the user can permanently delete the archive.
     */
    public function forceDelete(User $user, Archive $archive): bool
    {
        return $user->id === $archive->user_id;
    }

    /**
     * Determine if the user can toggle favorite.
     */
    public function toggleFavorite(User $user, Archive $archive): bool
    {
        return $user->id === $archive->user_id;
    }
}
