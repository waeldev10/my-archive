<?php

declare(strict_types=1);

namespace Modules\Archives\Events;

use Modules\Archives\Models\Archive;

/**
 * Dispatched when an archive's favorite status is toggled.
 * Carries the previous and new state for the listener to determine
 * the correct action label ('favorited' vs 'unfavorited').
 */
class ArchiveFavorited
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Archive $archive,
        public readonly bool $wasFavorited,  // previous state
        public readonly bool $isFavorited,   // new state
    ) {}
}
