<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\GetProducts;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetProductsQueryHandler
{
    public function __construct(
        private GetProductsQueryCompiler $compiler,
    ) {
    }

    /**
     * @return array<CatalogProductDTO>
     */
    public function __invoke(GetProductsQuery $query): array
    {
        $stmt = $this->compiler->compile(
            search: $query->search,
            filters: [],
            page: $query->page,
            limit: $query->limit
        );
        $result = $stmt->executeQuery();

        $productData = $result->fetchAllAssociative();

        return array_map(function ($product) {
            return new CatalogProductDTO(
                $product['name'],
                $product['description'],
                $product['slug'],
            );
        },
            $productData);
    }
}
