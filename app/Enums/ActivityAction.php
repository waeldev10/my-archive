<?php

declare(strict_types=1);

namespace App\Enums;

enum ActivityAction: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case Restored = 'restored';
    case Favorited = 'favorited';
    case Tagged = 'tagged';
}
