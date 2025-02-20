<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\GetProducts;

class CatalogProductDTO
{
    public function __construct(
        public string $name,
        public string $description,
        public string $slug,
    ) {
    }
}
