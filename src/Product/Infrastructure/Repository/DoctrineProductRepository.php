<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Repository;

use App\Common\Infrastructure\Repository\DoctrineRepositoryTrait;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Repository\ProductRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class DoctrineProductRepository extends ServiceEntityRepository implements ProductRepository
{
    /**
     * @use DoctrineRepositoryTrait<Product>
     */
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        // Pass the actual entity class here
        parent::__construct($registry, Product::class);
    }
}
