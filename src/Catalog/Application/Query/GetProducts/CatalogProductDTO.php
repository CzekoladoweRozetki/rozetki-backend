<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\GetProducts;

class CatalogProductDTO
{
    /**
     * @param array<int, array<string, string>> $categories
     */
    public function __construct(
        public string $name,
        public string $description,
        public string $slug,
        public array $categories,
    ) {
    }
}
