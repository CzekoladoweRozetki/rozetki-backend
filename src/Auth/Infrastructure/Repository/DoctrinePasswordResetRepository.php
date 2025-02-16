<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Repository;

use App\Auth\Domain\Entity\PasswordResetToken;
use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Repository\PasswordResetTokenRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PasswordResetToken>
 */
class DoctrinePasswordResetRepository extends ServiceEntityRepository implements PasswordResetTokenRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetToken::class);
    }

    public function save(PasswordResetToken $token): void
    {
        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();
    }

    public function findOneByToken(string $token): ?PasswordResetToken
    {
        return $this->findOneBy(['token' => $token]);
    }

    public function findOneByUser(User $user): ?PasswordResetToken
    {
        return $this->findOneBy(['user' => $user]);
    }

    public function remove(PasswordResetToken $passwordResetToken): void
    {
        $this->getEntityManager()->remove($passwordResetToken);
        $this->getEntityManager()->flush();
    }
}
