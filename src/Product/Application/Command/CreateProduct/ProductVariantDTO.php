<?php

declare(strict_types=1);

namespace App\Product\Application\Command\CreateProduct;

use Symfony\Component\Uid\Uuid;

class ProductVariantDTO
{
    /**
     * @param array<int, Uuid> $attributeValues
     */
    public function __construct(
        public string $name,
        public string $description,
        public ?string $slug = null,
        public array $attributeValues = [],
    ) {
    }
}
