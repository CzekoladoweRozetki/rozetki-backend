<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Catalog\Application\Query\GetProduct\GetProductQuery;
use App\Catalog\Infrastructure\Api\Resource\CatalogProduct;
use App\Common\Application\Query\QueryBus;

/**
 * @implements ProviderInterface<CatalogProduct>
 */
class CatalogProductSingleProvider implements ProviderInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $query = new GetProductQuery($uriVariables['id']);
        $product = $this->queryBus->query($query);

        return $product ? new CatalogProduct(
            $product['slug'],
            $product['name'],
            $product['description'],
        ) : null;
    }
}
