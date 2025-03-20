<?php

declare(strict_types=1);

namespace App\Product\Domain\Event\Partial;

use App\Product\Domain\Entity\ProductVariantAttribute;

readonly class ProductVariant
{
    /**
     * @param array<int, string> $attributeValues
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public string $slug,
        public array $attributeValues = [],
    ) {
    }

    public static function fromVariant(\App\Product\Domain\Entity\ProductVariant $variant): self
    {
        return new self(
            $variant->getId()->toString(),
            $variant->getName(),
            $variant->getDescription(),
            $variant->getSlug(),
            $variant->getAttributes()->map(
                /* @var  ProductVariantAttribute $attributeValue */
                fn ($attributeValue) => $attributeValue->getAttributeValueId()->toString(),
            )->toArray()
        );
    }
}
