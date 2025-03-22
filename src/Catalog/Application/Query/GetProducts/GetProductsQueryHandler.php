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
            limit: $query->limit,
            categorySlug: $query->categorySlug,
            attributes: $query->attributes
        );
        $result = $stmt->executeQuery();

        $productData = $result->fetchAllAssociative();

        return array_map(function ($product) {
            $data = $product['data'] !== (null) ? json_decode($product['data'], true) : [];
            $data['categories'] = $data['categories'] ?? [];
            $data['attributes'] = $data['attributes'] ?? [];

            return new CatalogProductDTO(
                $product['name'],
                $product['description'],
                $product['slug'],
                $data['categories'],
                $data['attributes']
            );
        },
            $productData);
    }
}
