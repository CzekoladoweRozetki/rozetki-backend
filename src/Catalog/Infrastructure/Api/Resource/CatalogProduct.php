<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Catalog\Infrastructure\Api\Provider\CatalogProductSingleProvider;

#[ApiResource(
    operations: [
        new Get(provider: CatalogProductSingleProvider::class),
    ]
)]
class CatalogProduct
{
    public function __construct(
        #[ApiProperty(identifier: true, description: 'The slug of the product')]
        public string $id,
        public string $uuid,
        public string $name,
        public string $description,
    ) {
    }
}
