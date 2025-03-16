<?php

declare(strict_types=1);

namespace App\Attribute\Application\Query\GetAttributeByIdQuery;

use App\Attribute\Domain\Exception\GetAttributeByIdQuery\AttributeNotFoundException;
use App\Attribute\Domain\Repository\AttributeRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetAttributeByIdQueryHandler
{
    public function __construct(
        private AttributeRepository $attributeRepository,
    ) {
    }

    public function __invoke(GetAttributeByIdQuery $query): AttributeDTO
    {
        $attribute = $this->attributeRepository->findOneById($query->id);

        if (!$attribute) {
            throw new AttributeNotFoundException('Attribute not found');
        }

        return new AttributeDTO(
            id: $attribute->getId(),
            name: $attribute->getName(),
            slug: $attribute->getSlug(),
            values: array_map(
                fn ($value) => new AttributeValueDTO(
                    id: $value->getId(),
                    value: $value->getValue(),
                    slug: $value->getSlug(),
                    attributeId: $value->getAttribute()->getId()
                ),
                $attribute->getValues()->toArray()
            ),
            parentId: $attribute->getParent()?->getId()
        );
    }
}
