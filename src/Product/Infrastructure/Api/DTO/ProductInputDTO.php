<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api\DTO;

readonly class ProductInputDTO
{
    public function __construct(
        public string $name,
        public string $description,
        /**
         * @var ProductVariantInputDTO[]
         */
        public array $variants = [],
        /**
         * @var array<int, string>
         */
        public array $categories = [],
    ) {
    }
}
