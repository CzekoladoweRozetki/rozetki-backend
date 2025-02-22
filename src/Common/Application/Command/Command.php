<?php

namespace App\Common\Application\Command;

use App\Common\Infrastructure\Security\AuthorizableMessage;
use App\Common\Infrastructure\Security\ExecutionContext;

abstract readonly class Command implements AuthorizableMessage
{
    private ExecutionContext $executionContext;

    public function __construct(ExecutionContext $executionContext = ExecutionContext::Web)
    {
        $this->executionContext = $executionContext;
    }

    public function getExecutionContext(): ExecutionContext
    {
        return $this->executionContext ?? ExecutionContext::Web;
    }
}
