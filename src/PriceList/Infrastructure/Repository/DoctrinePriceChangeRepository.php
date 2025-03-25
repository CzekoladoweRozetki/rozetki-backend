<?php

declare(strict_types=1);

namespace App\PriceList\Infrastructure\Repository;

use App\Common\Infrastructure\Repository\DoctrineRepositoryTrait;
use App\PriceList\Domain\Entity\PriceChange;
use App\PriceList\Domain\Repository\PriceChangeRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PriceChange>
 */
class DoctrinePriceChangeRepository extends ServiceEntityRepository implements PriceChangeRepository
{
    /**
     * @use DoctrineRepositoryTrait<PriceChange>
     */
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceChange::class);
    }
}
