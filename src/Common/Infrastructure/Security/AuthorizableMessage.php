<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Security;

interface AuthorizableMessage
{
    public function getExecutionContext(): ExecutionContext;
}
