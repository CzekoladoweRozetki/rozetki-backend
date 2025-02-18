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
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Post(input: ProductInputDTO::class, processor: ProductPostProcessor::class),
        new Get(provider: ProductSingleProvider::class),
        new Delete(provider: ProductSingleProvider::class, processor: ProductDeleteProcessor::class),
    ],
    normalizationContext: ['groups' => ['product']],
)]
readonly class Product
{
    /**
     * @param array<ProductVariant> $variants
     */
    public function __construct(
        #[ApiProperty(identifier: true)]
        #[Groups(['product'])]
        public string $id,
        #[Groups(['product'])]
        public string $name,
        #[Groups(['product'])]
        public string $description,
        #[Groups(['product'])]
        public array $variants = [],
    ) {
    }
}
