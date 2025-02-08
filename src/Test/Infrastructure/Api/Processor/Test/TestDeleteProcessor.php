<?php

declare(strict_types=1);

namespace App\Test\Infrastructure\Api\Processor\Test;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Common\Application\Command\CommandBus;
use App\Test\Application\Command\RemoveTestCommand;
use App\Test\Infrastructure\Api\Resource\Test;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<Test, Test>
 */
class TestDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new RemoveTestCommand(Uuid::fromString($data->id));

        $this->commandBus->dispatch($command);

        return $data;
    }
}
