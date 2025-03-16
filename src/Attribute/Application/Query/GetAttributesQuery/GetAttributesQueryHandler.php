<?php

declare(strict_types=1);

namespace App\Attribute\Application\Query\GetAttributesQuery;

use App\Attribute\Domain\Repository\AttributeRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetAttributesQueryHandler
{
    public function __construct(
        private AttributeRepository $attributeRepository,
    ) {
    }

    /**
     * @return AttributeDTO[]
     */
    public function __invoke(GetAttributesQuery $query): array
    {
        $attributes = $this->attributeRepository->findAttributes();

        return array_map(function ($attribute) {
            return new AttributeDTO(
                id: $attribute->getId(),
                name: $attribute->getName(),
                slug: $attribute->getSlug(),
                values: array_map(function ($value) {
                    return new AttributeValueDTO(
                        id: $value->getId(),
                        value: $value->getValue(),
                        slug: $value->getSlug(),
                        attributeId: $value->getAttribute()->getId()
                    );
                }, $attribute->getValues()->toArray()),
                parentId: $attribute->getParent()?->getId()
            );
        }, $attributes);
    }
}
