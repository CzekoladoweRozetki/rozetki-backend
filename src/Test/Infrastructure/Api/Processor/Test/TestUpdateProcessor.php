<?php

declare(strict_types=1);

namespace App\Test\Infrastructure\Api\Processor\Test;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Common\Application\Command\CommandBus;
use App\Test\Application\Command\UpdateTestCommand;
use App\Test\Infrastructure\Api\Resource\Test;
use Symfony\Component\Uid\Uuid;

class TestUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    /**
     * @param Test $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new UpdateTestCommand(
            Uuid::fromString($data->id),
            $data->name,
        );

        $this->commandBus->dispatch($command);

        return $data;
    }
}
