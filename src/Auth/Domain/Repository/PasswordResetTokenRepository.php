<?php

declare(strict_types=1);

namespace App\Auth\Domain\Repository;

use App\Auth\Domain\Entity\PasswordResetToken;
use App\Auth\Domain\Entity\User;

interface PasswordResetTokenRepository
{
    public function save(PasswordResetToken $token): void;

    public function findOneByToken(string $token): ?PasswordResetToken;

    public function findOneByUser(User $user): ?PasswordResetToken;

    public function remove(PasswordResetToken $passwordResetToken): void;
}
