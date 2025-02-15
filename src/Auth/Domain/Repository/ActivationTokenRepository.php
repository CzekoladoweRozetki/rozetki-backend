<?php

declare(strict_types=1);

namespace App\Auth\Domain\Repository;

use App\Auth\Domain\Entity\ActivationToken;

interface ActivationTokenRepository
{
    public function save(ActivationToken $activationToken): void;

    public function findByToken(string $token): ?ActivationToken;

    public function delete(ActivationToken $activationToken): void;

    public function countUserTokens(string $getEmail): int;
}
