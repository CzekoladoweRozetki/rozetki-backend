<?php

declare(strict_types=1);

namespace App\Attribute\Infrastructure\Api\DTO;

readonly class AttributeInputDTO
{
    /**
     * @param array<int, string> $values
     */
    public function __construct(
        public string $name,
        public array $values = [],
        public ?string $parentId = null,
    ) {
    }
}
