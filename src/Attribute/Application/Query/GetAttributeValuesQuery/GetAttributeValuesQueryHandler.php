<?php

declare(strict_types=1);

namespace App\Attribute\Application\Query\GetAttributeValuesQuery;

use App\Attribute\Domain\Repository\AttributeValueRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetAttributeValuesQueryHandler
{
    public function __construct(
        private AttributeValueRepository $attributeValueRepository,
    ) {
    }

    /**
     * @return AttributeValueDTO[]
     */
    public function __invoke(GetAttributeValuesQuery $query): array
    {
        $attributeValues = $this->attributeValueRepository->findByAttributeValueIds($query->ids);

        return array_map(function ($attributeValue) {
            return new AttributeValueDTO(
                id: $attributeValue->getId(),
                value: $attributeValue->getValue(),
                valueSlug: $attributeValue->getSlug(),
                attributeId: $attributeValue->getAttribute()->getId(),
                attributeName: $attributeValue->getAttribute()->getName(),
                attributeSlug: $attributeValue->getAttribute()->getSlug(),
            );
        }, $attributeValues);
    }
}
