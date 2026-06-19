<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUlids;

trait UsesUlid
{
    use HasUlids;

    /**
     * Initialize the ULID trait for this model.
     *
     * Sets the model to use unique string IDs (ULIDs) instead of
     * auto-incrementing integers.
     */
    public function initializeUsesUlid(): void
    {
        $this->usesUniqueIds = true;
    }
}
