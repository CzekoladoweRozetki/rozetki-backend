<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Repository;

use App\Auth\Domain\Entity\PasswordResetToken;
use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Repository\PasswordResetTokenRepository;
use App\Common\Infrastructure\Repository\DoctrineRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PasswordResetToken>
 */
class DoctrinePasswordResetRepository extends ServiceEntityRepository implements PasswordResetTokenRepository
{
    /**
     * @use DoctrineRepositoryTrait<PasswordResetToken>
     */
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetToken::class);
    }

    public function findOneByToken(string $token): ?PasswordResetToken
    {
        return $this->findOneBy(['id' => $token]);
    }

    public function findOneByUser(User $user): ?PasswordResetToken
    {
        return $this->findOneBy(['user' => $user]);
    }
}
