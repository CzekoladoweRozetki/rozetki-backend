<?php

declare(strict_types=1);

namespace App\Attribute\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [],
    normalizationContext: ['groups' => ['attribute_value']],
)]
class AttributeValue
{
    public function __construct(
        #[Groups(['attribute_value', 'attribute'])]
        public string $id,
        #[Groups(['attribute_value', 'attribute'])]
        public string $value,
        #[Groups(['attribute_value', 'attribute'])]
        public string $attributeId,
    ) {
    }
}
