<?php

declare(strict_types=1);

namespace App\Api\Processor\Test;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\DTO\Test\TestInputDTO;
use App\Common\Application\Command\CommandBus;
use App\Test\Application\Command\TestCommand;
use Symfony\Component\Uid\Uuid;

class TessCreateProcessor implements ProcessorInterface
{

    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    /**
     * @param TestInputDTO $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new TestCommand(
            Uuid::v4(),
            $data->name
        );

        $this->commandBus->dispatch($command);

        return $data;
    }
}
