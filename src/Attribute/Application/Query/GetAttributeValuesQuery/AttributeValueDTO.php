<?php

declare(strict_types=1);

namespace App\Attribute\Application\Query\GetAttributeValuesQuery;

use Symfony\Component\Uid\Uuid;

readonly class AttributeValueDTO
{
    public function __construct(
        public Uuid $id,
        public string $value,
        public string $valueSlug,
        public Uuid $attributeId,
        public string $attributeName,
        public string $attributeSlug,
    ) {
    }
}
