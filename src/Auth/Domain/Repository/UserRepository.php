<?php

declare(strict_types=1);

namespace App\Auth\Domain\Repository;

use App\Auth\Domain\Entity\User;
use App\Common\Domain\Repository\Repository;
use Symfony\Component\Uid\Uuid;

/**
 * @extends Repository<User>
 */
interface UserRepository extends Repository
{
    public function findOneByEmail(string $email): ?User;

    public function getUserById(Uuid $id): ?User;
}
