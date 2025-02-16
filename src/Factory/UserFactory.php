<?php

declare(strict_types=1);

namespace App\Factory;

use App\Auth\Domain\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
class UserFactory extends PersistentObjectFactory
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

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

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (User $user) {
                if ($user->getPassword()) {
                    $hashedPassword = $this->passwordHasher->hashPassword(
                        $user,
                        $user->getPassword()
                    );
                    $user->setPassword($hashedPassword);
                }
            });
    }
}
