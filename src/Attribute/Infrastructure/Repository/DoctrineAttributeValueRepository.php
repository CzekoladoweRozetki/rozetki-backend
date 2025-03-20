<?php

declare(strict_types=1);

namespace App\Attribute\Infrastructure\Repository;

use App\Attribute\Domain\Entity\AttributeValue;
use App\Attribute\Domain\Repository\AttributeValueRepository;
use App\Common\Infrastructure\Repository\DoctrineRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AttributeValue>
 */
class DoctrineAttributeValueRepository extends ServiceEntityRepository implements AttributeValueRepository
{
    /**
     * @use DoctrineRepositoryTrait<AttributeValue>
     */
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributeValue::class);
    }

    public function findByAttributeValueIds(array $ids): array
    {
        $qb = $this->createQueryBuilder('av');
        $qb->where($qb->expr()->in('av.id', ':ids'));
        $qb->setParameter('ids', $ids);

        return $qb->getQuery()->getResult();
    }
}
