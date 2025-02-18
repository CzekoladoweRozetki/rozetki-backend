<?php

declare(strict_types=1);

namespace App\Auth\Domain\Repository;

use App\Auth\Domain\Entity\PasswordResetToken;
use App\Auth\Domain\Entity\User;
use App\Common\Domain\Repository\Repository;

/**
 * @extends Repository<PasswordResetToken>
 */
interface PasswordResetTokenRepository extends Repository
{
    public function findOneByToken(string $token): ?PasswordResetToken;

    public function findOneByUser(User $user): ?PasswordResetToken;
}
