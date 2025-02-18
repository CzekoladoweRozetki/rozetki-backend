<?php

declare(strict_types=1);

namespace App\Product\Application\Query\DTO;

use Symfony\Component\Uid\Uuid;

readonly class ProductDTO
{
    /**
     * @param array<ProductVariantDTO> $variants
     */
    public function __construct(
        public Uuid $id,
        public string $name,
        public string $description,
        public array $variants,
    ) {
    }
}
