<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [],
    normalizationContext: ['groups' => ['productVariant']]
)]
class ProductVariant
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        #[Groups(['productVariant', 'product'])]
        public string $id,
        #[Groups(['productVariant', 'product'])]
        public string $name,
        #[Groups(['productVariant', 'product'])]
        public string $description,
        #[Groups(['productVariant', 'product'])]
        public string $slug,
    ) {
    }
}
