<?php

declare(strict_types=1);

namespace App\Auth\Application\RegisterUserCommand;

use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
class RegisterUserCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(RegisterUserCommand $command): void
    {
        $user = new User(
            id: $command->id,
            email: $command->email,
            password: $command->password
        );

        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));

        $this->userRepository->save($user);
    }
}
