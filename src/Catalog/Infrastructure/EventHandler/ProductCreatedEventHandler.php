<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\EventHandler;

use App\Attribute\Application\Query\GetAttributeValuesQuery\AttributeValueDTO;
use App\Attribute\Application\Query\GetAttributeValuesQuery\GetAttributeValuesQuery;
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

            // Attributes
            $data['attributes'] = [];
            $productAttributeValueIds = array_map(
                fn ($attributeValue) => Uuid::fromString($attributeValue),
                $event->attributes
            );
            $VariantAttributeValueIds = array_map(
                fn ($attributeValue) => Uuid::fromString($attributeValue),
                $variant->attributeValues
            );
            $attributeValueIds = array_merge($productAttributeValueIds, $VariantAttributeValueIds);
            $query = new GetAttributeValuesQuery(
                $attributeValueIds, ExecutionContext::Internal
            );
            /**
             * @var AttributeValueDTO[] $attributesValues
             */
            $attributesValues = $this->queryBus->query($query);

            foreach ($attributesValues as $attributeValue) {
                if (!isset($data['attributes'][$attributeValue->attributeSlug])) {
                    $data['attributes'][$attributeValue->attributeSlug] = [
                        'name' => $attributeValue->attributeName,
                        'slug' => $attributeValue->attributeSlug,
                        'id' => $attributeValue->attributeId,
                        'values' => array_map(
                            fn ($attributeValue) => [
                                'name' => $attributeValue->value,
                                'slug' => $attributeValue->valueSlug,
                                'id' => $attributeValue->id,
                            ],
                            $attributesValues
                        ),
                    ];
                }
            }

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
