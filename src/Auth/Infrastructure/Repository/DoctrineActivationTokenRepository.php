<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Repository;

use App\Auth\Domain\Entity\ActivationToken;
use App\Auth\Domain\Repository\ActivationTokenRepository;
use App\Common\Infrastructure\Repository\DoctrineRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActivationToken>
 */
class DoctrineActivationTokenRepository extends ServiceEntityRepository implements ActivationTokenRepository
{
    /**
     * @use DoctrineRepositoryTrait<ActivationToken>
     */
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivationToken::class);
    }


    public function findByToken(string $token): ?ActivationToken
    {
        return $this->findOneBy(['token' => $token]);
    }

    public function countUserTokens(string $getEmail): int
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('count(a.id)');
        $qb->where('a.email = :email');
        $qb->setParameter('email', $getEmail);

        return (int)$qb->getQuery()->getSingleScalarResult();
    }
}
