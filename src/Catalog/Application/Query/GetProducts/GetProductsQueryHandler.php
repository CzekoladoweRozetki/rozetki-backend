<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\GetProducts;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetProductsQueryHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array<CatalogProductDTO>
     */
    public function __invoke(GetProductsQuery $query): array
    {
        $conn = $this->entityManager->getConnection();

        $sql = '
            SELECT * FROM catalog_product
            WHERE name LIKE :search
            LIMIT :limit OFFSET :offset
        ';

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('search', '%'.$query->search.'%');
        $stmt->bindValue('limit', $query->limit);
        $stmt->bindValue('offset', $query->limit * ($query->page - 1));
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
