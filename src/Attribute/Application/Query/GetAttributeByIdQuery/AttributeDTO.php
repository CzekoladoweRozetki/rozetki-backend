<?php

declare(strict_types=1);

namespace App\Attribute\Application\Query\GetAttributeByIdQuery;

use Symfony\Component\Uid\Uuid;

class AttributeDTO
{
    /**
     * @param array<AttributeValueDTO> $values
     */
    public function __construct(
        public Uuid $id,
        public string $name,
        public string $slug,
        public array $values = [],
        public ?Uuid $parentId = null,
    ) {
    }
}
