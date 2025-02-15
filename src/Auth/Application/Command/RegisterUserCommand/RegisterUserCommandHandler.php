<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\RegisterUserCommand;

use App\Auth\Domain\Entity\ActivationToken;
use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Event\UserCreatedEvent;
use App\Auth\Domain\Repository\ActivationTokenRepository;
use App\Auth\Domain\Repository\UserRepository;
use App\Common\Application\Event\EventBus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
class RegisterUserCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private EventBus $eventBus,
        private ActivationTokenRepository $activationTokenRepository,
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

        $activationToken = ActivationToken::create($user->getEmail());

        $this->activationTokenRepository->save($activationToken);

        $event = new UserCreatedEvent(
            id: $user->getId()->toString(),
            email: $user->getEmail(),
            token: $activationToken->getToken()
        );

        $this->eventBus->dispatch($event);
    }
}
