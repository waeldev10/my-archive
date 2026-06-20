<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Modules\Core\Traits\UsesUlid;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * Custom Sanctum PersonalAccessToken model using ULID primary keys.
 *
 * Overrides the default Sanctum token model to support ULID-format IDs
 * instead of auto-incrementing integers, consistent with the rest of
 * the application.
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use UsesUlid;

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;
}
