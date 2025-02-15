<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Auth\Application\Command\ActivateUser\ActivateUserCommand;
use App\Auth\Infrastructure\Api\Resource\UserActivation;
use App\Common\Application\Command\CommandBus;

/**
 * @implements ProcessorInterface<UserActivation, null>
 */
class UserActivationProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new ActivateUserCommand($data->token);

        $this->commandBus->dispatch($command);

        return null;
    }
}
