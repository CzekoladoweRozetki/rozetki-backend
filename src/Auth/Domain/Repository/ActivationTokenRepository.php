<?php

declare(strict_types=1);

namespace App\Auth\Domain\Repository;

use App\Auth\Domain\Entity\ActivationToken;
use App\Common\Domain\Repository\Repository;

/**
 * @extends Repository<ActivationToken>
 */
interface ActivationTokenRepository extends Repository
{
    public function findByToken(string $token): ?ActivationToken;

    public function countUserTokens(string $getEmail): int;
}
