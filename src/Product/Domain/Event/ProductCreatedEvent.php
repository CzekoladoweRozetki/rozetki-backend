<?php

declare(strict_types=1);

namespace App\Product\Domain\Event;

use App\Common\Domain\Event;
use App\Product\Domain\Event\Partial\ProductVariant;

readonly class ProductCreatedEvent extends Event
{
    /**
     * @param array<ProductVariant> $variants
     * @param array<int, string>    $categories
     */
    public function __construct(
        public string $productId,
        public string $name,
        public string $description,
        public array $variants,
        public array $categories,
    ) {
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array<ProductVariant>
     */
    public function getVariants(): array
    {
        return $this->variants;
    }
}
