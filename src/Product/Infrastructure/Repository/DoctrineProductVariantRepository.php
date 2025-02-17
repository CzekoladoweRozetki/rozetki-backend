<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Repository;

use App\Common\Infrastructure\Repository\DoctrineRepositoryTrait;
use App\Product\Domain\Entity\ProductVariant;
use App\Product\Domain\Repository\ProductVariantRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductVariant>
 */
class DoctrineProductVariantRepository extends ServiceEntityRepository implements ProductVariantRepository
{
    /**
     * @use DoctrineRepositoryTrait<ProductVariant>
     */
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductVariant::class);
    }

    public function findOneBySlug(string $slug): ?ProductVariant
    {
        return $this->findOneBy(['slug' => $slug]);
    }
}
