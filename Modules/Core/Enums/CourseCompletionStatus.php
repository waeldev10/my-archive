<?php

declare(strict_types=1);

namespace Modules\Core\Enums;

enum CourseCompletionStatus: string
{
    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
