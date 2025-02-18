<?php

declare(strict_types=1);

namespace App\Product\Application\Command\CreateProduct;

class ProductVariantDTO
{
    public function __construct(
        public string $name,
        public string $description,
        public ?string $slug = null,
    ) {
    }
}
