<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Auth\Application\Command\RegisterUserCommand\RegisterUserCommand;
use App\Auth\Infrastructure\Api\DTO\UserInputDTO;
use App\Auth\Infrastructure\Api\Resource\User;
use App\Common\Application\Command\CommandBus;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<UserInputDTO, User>
 */
class UserPostProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new RegisterUserCommand(Uuid::v4(), $data->email, $data->password);

        $this->commandBus->dispatch($command);

        return new User(
            id: $command->id->toString(),
            email: $command->email,
        );
    }
}
