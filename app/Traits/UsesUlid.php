<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUlids;

trait UsesUlid
{
    use HasUlids;

    public function initializeUsesUlid(): void
    {
        $this->usesUlids();
    }
}
