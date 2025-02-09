<?php

declare(strict_types=1);

namespace App\Factory;

use App\Auth\Domain\Entity\User;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
class UserFactory extends PersistentObjectFactory
{
    protected function defaults(): array|callable
    {
        return [
            'id' => Uuid::v4(),
            'email' => self::faker()->email(),
            'password' => self::faker()->password(),
        ];
    }

    public static function class(): string
    {
        return User::class;
    }
}
