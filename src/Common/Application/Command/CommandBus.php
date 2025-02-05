<?php

namespace App\Common\Application\Command;

use Symfony\Component\Messenger\MessageBusInterface;

class CommandBus
{
    public function __construct(
        private MessageBusInterface $commandBus
    )
    {
    }

    public function dispatch(Command $command): void
    {
        $this->commandBus->dispatch($command);
    }
}
