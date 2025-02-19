<?php

declare(strict_types=1);

namespace App\Product\Domain\Event\Partial;

readonly class ProductVariant
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public string $slug,
    ) {
    }

    public static function fromVariant(\App\Product\Domain\Entity\ProductVariant $variant): self
    {
        return new self(
            $variant->getId()->toString(),
            $variant->getName(),
            $variant->getDescription(),
            $variant->getSlug()
        );
    }
}
