<?php

declare(strict_types=1);

namespace Modules\Core\Enums;

enum ProjectStatus: string
{
    case Idea = 'idea';
    case Planning = 'planning';
    case Active = 'active';
    case Paused = 'paused';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
