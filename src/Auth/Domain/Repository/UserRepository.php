<?php

declare(strict_types=1);

namespace App\Auth\Domain\Repository;

use App\Auth\Domain\Entity\User;
use Symfony\Component\Uid\Uuid;

interface UserRepository
{
    public function findOneByEmail(string $email): ?User;

    public function save(User $user): void;

    public function remove(User $user): void;

    public function getUserById(Uuid $id): ?User;
}
