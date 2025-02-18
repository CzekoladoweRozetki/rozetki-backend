<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
class ProductVariant
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public string $id,
        public string $name,
        public string $description,
        public string $slug,
    ) {
    }
}
