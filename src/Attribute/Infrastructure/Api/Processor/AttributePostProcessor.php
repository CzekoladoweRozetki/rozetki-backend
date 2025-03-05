<?php

declare(strict_types=1);

namespace App\Attribute\Infrastructure\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Attribute\Application\Command\CreateAttribute\CreateAttributeCommand;
use App\Attribute\Application\Query\GetAttributeByIdQuery\AttributeDTO;
use App\Attribute\Application\Query\GetAttributeByIdQuery\GetAttributeByIdQuery;
use App\Attribute\Infrastructure\Api\DTO\AttributeInputDTO;
use App\Attribute\Infrastructure\Api\Resource\Attribute;
use App\Attribute\Infrastructure\Api\Resource\AttributeValue;
use App\Common\Application\Command\CommandBus;
use App\Common\Application\Query\QueryBus;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<AttributeInputDTO, Attribute>
 */
class AttributePostProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
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

        $query = new GetAttributeByIdQuery($command->id);
        /** @var AttributeDTO $attribute */
        $attribute = $this->queryBus->query($query);

        $values = array_map(
            fn ($value) => new AttributeValue(
                $value->id->toString(),
                $value->value,
                $value->attributeId->toString(),
            ),
            $attribute->values
        );

        return new Attribute(
            $id->toString(),
            $attribute->name,
            $values,
            $attribute->parentId?->toString(),
        );
    }
}
