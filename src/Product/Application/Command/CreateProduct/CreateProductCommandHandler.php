<?php

declare(strict_types=1);

namespace App\Product\Application\Command\CreateProduct;

use App\Product\Domain\Entity\Product;
use App\Product\Domain\Repository\ProductRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateProductCommandHandler
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {
    }

    public function __invoke(CreateProductCommand $command): void
    {
        $product = new Product(
            $command->id,
            $command->name,
            $command->description
        );

        $this->productRepository->save($product);
    }
}
