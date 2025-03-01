<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\EventHandler;

use App\Catalog\Domain\Entity\CatalogProduct;
use App\Catalog\Domain\Repository\CatalogProductRepository;
use App\Category\Application\Query\GetCategories\CategoryDTO;
use App\Category\Application\Query\GetCategories\GetCategoriesQuery;
use App\Common\Application\Query\QueryBus;
use App\Common\Infrastructure\Security\ExecutionContext;
use App\Product\Domain\Event\ProductCreatedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
class ProductCreatedEventHandler
{
    public function __construct(
        private CatalogProductRepository $catalogProductRepository,
        private QueryBus $queryBus,
    ) {
    }

    public function __invoke(ProductCreatedEvent $event): void
    {
        foreach ($event->getVariants() as $variant) {
            $catalogProduct = $this->catalogProductRepository->findOneById(Uuid::fromString($variant->id));
            if ($catalogProduct) {
                continue;
            }

            /** @var CategoryDTO[] $categories */
            $categories = $this->queryBus->query(
                new GetCategoriesQuery($event->categories, executionContext: ExecutionContext::Internal)
            );
            $data = [];
            $data['categories'] = array_map(fn ($category) => ['name' => $category->name, 'slug' => $category->slug],
                $categories);

            $catalogProduct = new CatalogProduct(
                Uuid::fromString($variant->id),
                $variant->name,
                $variant->description ?? $event->description,
                $variant->slug,
                data: $data
            );
            $this->catalogProductRepository->save($catalogProduct);
        }
    }
}
