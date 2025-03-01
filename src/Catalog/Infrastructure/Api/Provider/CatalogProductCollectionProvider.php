<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Catalog\Application\Query\GetProducts\GetProductsQuery;
use App\Catalog\Infrastructure\Api\Resource\CatalogProduct;
use App\Common\Application\Query\QueryBus;

/**
 * @implements ProviderInterface<CatalogProduct>
 */
class CatalogProductCollectionProvider implements ProviderInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $query = new GetProductsQuery(
            search: $context['filters']['search'] ?? null,
            page: isset($context['filters']['page']) ? (int) $context['filters']['page'] : 1,
            limit: isset($context['filters']['itemsPerPage']) ? (int) $context['filters']['itemsPerPage'] : 10,
            categorySlug: $context['filters']['c'] ?? null,
        );

        $result = $this->queryBus->query($query);

        return array_map(function ($product) {
            return new CatalogProduct(
                id: $product->slug,
                name: $product->name,
                description: $product->description,
                categories: $product->categories,
            );
        }, $result);
    }
}
