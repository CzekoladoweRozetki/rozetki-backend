<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Product\Infrastructure\Api\DTO\ProductInputDTO;
use App\Product\Infrastructure\Api\Processor\ProductDeleteProcessor;
use App\Product\Infrastructure\Api\Processor\ProductPostProcessor;
use App\Product\Infrastructure\Api\Provider\ProductSingleProvider;

#[ApiResource(
    operations: [
        new Post(input: ProductInputDTO::class, processor: ProductPostProcessor::class),
        new Get(provider: ProductSingleProvider::class),
        new Delete(provider: ProductSingleProvider::class, processor: ProductDeleteProcessor::class),
    ]
)]
readonly class Product
{
    /**
     * @param array<ProductVariant> $variants
     */
    public function __construct(
        #[ApiProperty(identifier: true)]
        public string $id,
        public string $name,
        public string $description,
        public array $variants = [],
    ) {
    }
}
