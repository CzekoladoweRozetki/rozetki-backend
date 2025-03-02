<?php

declare(strict_types=1);

namespace App\Attribute\Infrastructure\Repository;

use App\Attribute\Domain\Entity\Attribute;
use App\Attribute\Domain\Repository\AttributeRepository;
use App\Common\Infrastructure\Repository\DoctrineRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Attribute>
 */
class DoctrineAttributeRepository extends ServiceEntityRepository implements AttributeRepository
{
    /**
     * @use DoctrineRepositoryTrait<Attribute>
     */
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attribute::class);
    }
}
