<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\GetProduct;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetProductQueryHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array<mixed>|null
     */
    public function __invoke(GetProductQuery $query): ?array
    {
        $connection = $this->entityManager->getConnection();

        $sql = '
            SELECT * FROM catalog_product
            WHERE slug = :slug
        ';

        $statement = $connection->prepare($sql);
        $statement->bindValue('slug', $query->slug);

        $result = $statement->executeQuery();
        $result = $result->fetchAssociative();

        return $result ?: null;
    }
}
