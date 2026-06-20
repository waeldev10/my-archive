<?php

declare(strict_types=1);

namespace Modules\Archives\DTOs;

class ArchiveData
{
    /**
     * @param array<string, mixed> $typeData Type-specific extension fields
     * @param array<int, string> $tags Tag names to associate
     */
    public function __construct(
        public readonly string $type,
        public readonly string $title,
        public readonly ?string $description = null,
        public readonly bool $isFavorite = false,
        public readonly array $tags = [],
        public readonly ?array $typeData = null,
    ) {}
}
