<?php

declare(strict_types=1);

namespace App\Attribute\Application\Query\GetAttributeValueByIdQuery;

use Symfony\Component\Uid\Uuid;

class AttributeValueDTO
{
    public function __construct(
        public Uuid $id,
        public string $value,
        public string $attributeName,
        public Uuid $attributeId,
    ) {
    }
}
