<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\Repository;

use App\Catalog\Domain\Entity\CatalogProduct;
use App\Catalog\Domain\Repository\CatalogProductRepository;
use App\Common\Infrastructure\Repository\DoctrineRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CatalogProduct>
 */
class DoctrineCatalogProductRepository extends ServiceEntityRepository implements CatalogProductRepository
{
    /**
     * @use DoctrineRepositoryTrait<CatalogProduct>
     */
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CatalogProduct::class);
    }
}
