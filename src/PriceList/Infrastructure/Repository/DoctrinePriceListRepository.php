<?php

declare(strict_types=1);

namespace App\PriceList\Infrastructure\Repository;

use App\Common\Infrastructure\Repository\DoctrineRepositoryTrait;
use App\PriceList\Domain\Entity\PriceList;
use App\PriceList\Domain\Repository\PriceListRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PriceList>
 */
class DoctrinePriceListRepository extends ServiceEntityRepository implements PriceListRepository
{
    /**
     * @use DoctrineRepositoryTrait<PriceList>
     */
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceList::class);
    }
}
