<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Auth\Application\Command\RequestPasswordReset\RequestPasswordResetCommand;
use App\Common\Application\Command\CommandBus;

class PasswordResetRequestProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new RequestPasswordResetCommand($data->email);

        $this->commandBus->dispatch($command);

        return null;
    }
}
