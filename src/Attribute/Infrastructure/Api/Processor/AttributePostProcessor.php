<?php

declare(strict_types=1);

namespace App\Attribute\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Attribute\Application\Command\CreateAttribute\CreateAttributeCommand;
use App\Attribute\Infrastructure\Api\DTO\AttributeInputDTO;
use App\Attribute\Infrastructure\Api\Resource\Attribute;
use App\Common\Application\Command\CommandBus;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<AttributeInputDTO, Attribute>
 */
class AttributePostProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $id = Uuid::v4();
        $command = new CreateAttributeCommand(
            $id,
            $data->name,
            $data->values,
            $data->parentId ? Uuid::fromString($data->parentId) : null,
        );

        try {
            $this->commandBus->dispatch($command);
        } catch (\Exception $exception) {
            throw $exception->getPrevious() ?? $exception;
        }

        return new Attribute(
            $id->toString(),
            $command->name,
            $command->values,
            $command->parentId?->toString() ?? null,
        );
    }
}
