<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api\DTO;

final class ProductVariantInputDTO
{
    /**
     * @param array<int, string> $attributeValues
     */
    public function __construct(
        public string $name,
        public string $description,
        public array $attributeValues = [],
    ) {
    }
}
