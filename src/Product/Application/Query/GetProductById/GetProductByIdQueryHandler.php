<?php

declare(strict_types=1);

namespace App\Product\Application\Query\GetProductById;

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

        return $product ? new ProductDTO(
            $product->getId(),
            $product->getName(),
            $product->getDescription()
        ) : null;
    }
}
