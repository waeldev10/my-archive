<?php

declare(strict_types=1);

namespace Modules\Core\Enums;

enum AiMessageRole: string
{
    case User = 'user';
    case Assistant = 'assistant';
}
