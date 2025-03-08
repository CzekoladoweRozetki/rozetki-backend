<?php

declare(strict_types=1);

namespace App\Attribute\Infrastructure\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Attribute\Application\Query\GetAttributesQuery\AttributeDTO;
use App\Attribute\Application\Query\GetAttributesQuery\GetAttributesQuery;
use App\Attribute\Infrastructure\Api\Resource\Attribute;
use App\Attribute\Infrastructure\Api\Resource\AttributeValue;
use App\Common\Application\Query\QueryBus;

/**
 * @implements ProviderInterface<Attribute>
 */
class AttributeCollectionProvider implements ProviderInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $query = new GetAttributesQuery();

        /** @var AttributeDTO[] $result */
        $result = $this->queryBus->query($query);

        return array_map(
            fn (AttributeDTO $attribute) => new Attribute(
                $attribute->id->toString(),
                $attribute->name,
                array_map(
                    fn ($value) => new AttributeValue(
                        $value->id->toString(),
                        $value->value,
                        $value->attributeId->toString(),
                    ),
                    $attribute->values
                ),
                $attribute->parentId?->toString(),
            ),
            $result
        );
    }
}
