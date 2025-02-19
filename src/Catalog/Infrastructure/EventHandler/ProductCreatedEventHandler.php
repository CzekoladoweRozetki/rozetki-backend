<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\EventHandler;

use App\Catalog\Domain\Entity\CatalogProduct;
use App\Catalog\Domain\Repository\CatalogProductRepository;
use App\Product\Domain\Event\ProductCreatedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
class ProductCreatedEventHandler
{
    public function __construct(
        private CatalogProductRepository $catalogProductRepository,
    ) {
    }

    public function __invoke(ProductCreatedEvent $event): void
    {
        foreach ($event->getVariants() as $variant) {
            $catalogProduct = $this->catalogProductRepository->findOneById(Uuid::fromString($variant->id));
            if ($catalogProduct) {
                continue;
            }
            $catalogProduct = new CatalogProduct(
                Uuid::fromString($variant->id),
                $variant->name,
                $variant->description ?? $event->description,
                $variant->slug
            );
            $this->catalogProductRepository->save($catalogProduct);
        }
    }
}
