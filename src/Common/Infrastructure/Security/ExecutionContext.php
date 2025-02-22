<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Security;

enum ExecutionContext
{
    case Web;
    case Console;
    case Internal;
}
