<?php

declare(strict_types=1);

namespace App\Test\Infrastructure\Repository;

use App\Test\Domain\Entity\Test;
use App\Test\Domain\Repository\TestRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class DoctrineTestRepository extends ServiceEntityRepository implements TestRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Test::class);
    }

    public function findOneById(Uuid $id): ?Test
    {
        return $this->find($id);
    }

    public function save(Test $test): void
    {
        $this->getEntityManager()->persist($test);
        $this->getEntityManager()->flush();
    }
}
