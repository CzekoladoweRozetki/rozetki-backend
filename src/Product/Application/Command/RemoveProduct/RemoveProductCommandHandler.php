<?php

declare(strict_types=1);

namespace App\Product\Application\Command\RemoveProduct;

use App\Product\Domain\Repository\ProductRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RemoveProductCommandHandler
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {
    }

    public function __invoke(RemoveProductCommand $command): void
    {
        $product = $this->productRepository->findById($command->id);

        if ($product) {
            $this->productRepository->remove($product);
        }
    }
}
