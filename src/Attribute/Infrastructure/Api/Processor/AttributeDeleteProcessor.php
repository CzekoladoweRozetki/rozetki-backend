<?php

declare(strict_types=1);

namespace App\Attribute\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Attribute\Application\Command\RemoveAttribute\RemoveAttributeCommand;
use App\Attribute\Infrastructure\Api\Resource\Attribute;
use App\Common\Application\Command\CommandBus;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<Attribute, null>
 */
class AttributeDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    /**
     * @param Attribute $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $command = new RemoveAttributeCommand(Uuid::fromString($data->id));

        $this->commandBus->dispatch($command);

        return null;
    }
}
