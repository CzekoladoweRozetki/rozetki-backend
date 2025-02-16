<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Auth\Application\Command\ResetPassword\ResetPasswordCommand;
use App\Auth\Infrastructure\Api\Resource\PasswordReset;
use App\Common\Application\Command\CommandBus;

/**
 * @implements ProcessorInterface<PasswordReset, null>
 */
class PasswordResetProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new ResetPasswordCommand($data->token, $data->newPassword);

        $this->commandBus->dispatch($command);

        return null;
    }
}
