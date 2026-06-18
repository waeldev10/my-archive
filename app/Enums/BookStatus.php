<?php

declare(strict_types=1);

namespace App\Enums;

enum BookStatus: string
{
    case ToRead = 'to_read';
    case Reading = 'reading';
    case Finished = 'finished';
    case Abandoned = 'abandoned';
}
