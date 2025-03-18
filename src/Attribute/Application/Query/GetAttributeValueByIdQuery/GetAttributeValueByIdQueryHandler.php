<?php

declare(strict_types=1);

namespace App\Attribute\Application\Query\GetAttributeValueByIdQuery;

use App\Attribute\Domain\Repository\AttributeValueRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetAttributeValueByIdQueryHandler
{
    public function __construct(
        private AttributeValueRepository $attributeValueRepository,
    ) {
    }

    public function __invoke(GetAttributeValueByIdQuery $query): AttributeValueDTO
    {
        $attributeValue = $this->attributeValueRepository->findOneById($query->id);

        return new AttributeValueDTO(
            id: $attributeValue->getId(),
            value: $attributeValue->getValue(),
            attributeName: $attributeValue->getAttribute()->getName(),
            attributeId: $attributeValue->getAttribute()->getId(),
        );
    }
}
