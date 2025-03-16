<?php

declare(strict_types=1);

namespace App\Attribute\Infrastructure\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Attribute\Application\Query\GetAttributeByIdQuery\AttributeDTO;
use App\Attribute\Application\Query\GetAttributeByIdQuery\GetAttributeByIdQuery;
use App\Attribute\Domain\Exception\GetAttributeByIdQuery\AttributeNotFoundException;
use App\Attribute\Infrastructure\Api\Resource\Attribute;
use App\Attribute\Infrastructure\Api\Resource\AttributeValue;
use App\Common\Application\Query\QueryBus;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProviderInterface<Attribute>
 */
class AttributeSingleProvider implements ProviderInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /* @var AttributeDTO $attribute */
        try {
            $attribute = $this->queryBus->query(new GetAttributeByIdQuery(Uuid::fromString($uriVariables['id'])));
        } catch (\Exception $exception) {
            if (null !== $exception->getPrevious() && $exception->getPrevious() instanceof AttributeNotFoundException) {
                return null;
            }
            throw $exception->getPrevious() ?? $exception;
        }

        return new Attribute(
            $attribute->id->toString(),
            $attribute->name,
            $attribute->slug,
            array_map(
                fn ($value) => new AttributeValue(
                    $value->id->toString(),
                    $value->value,
                    $value->slug,
                    $value->attributeId->toString(),
                ),
                $attribute->values
            ),
            $attribute->parentId?->toString(),
        );
    }
}
