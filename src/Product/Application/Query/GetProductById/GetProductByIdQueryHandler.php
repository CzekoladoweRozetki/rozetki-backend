<?php

declare(strict_types=1);

namespace App\Product\Application\Query\GetProductById;

use App\Product\Application\Query\DTO\ProductDTO;
use App\Product\Application\Query\DTO\ProductVariantDTO;
use App\Product\Domain\Entity\ProductVariant;
use App\Product\Domain\Repository\ProductRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetProductByIdQueryHandler
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {
    }

    public function __invoke(GetProductByIdQuery $query): ?ProductDTO
    {
        $product = $this->productRepository->findOneById($query->id);

        $variants = array_map(
            /**
             * @param ProductVariant $variant
             */
            fn ($variant) => new ProductVariantDTO(
                $variant->getId(),
                $variant->getName(),
                $variant->getDescription(),
                $variant->getSlug()
            ),
            $product->getVariants()->toArray()
        );

        return $product ? new ProductDTO(
            $product->getId(),
            $product->getName(),
            $product->getDescription(),
            $variants
        ) : null;
    }
}
