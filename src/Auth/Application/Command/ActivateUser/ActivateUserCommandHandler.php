<?php

namespace App\Auth\Application\Command\ActivateUser;

use App\Auth\Domain\Event\ActivationTokenExpiredEvent;
use App\Auth\Domain\Event\UserActivatedEvent;
use App\Auth\Domain\Exception\ActivationAttemptsExceedException;
use App\Auth\Domain\Exception\ActivationTokenExpiredException;
use App\Auth\Domain\Exception\TokenNotFoundException;
use App\Auth\Domain\Repository\ActivationTokenRepository;
use App\Auth\Domain\Repository\UserRepository;
use App\Common\Application\Event\EventBus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ActivateUserCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private ActivationTokenRepository $activationTokenRepository,
        private EventBus $eventBus,
    ) {
    }

    public function __invoke(ActivateUserCommand $command): void
    {
        $token = $this->activationTokenRepository->findByToken($command->token);

        if (!$token) {
            throw new TokenNotFoundException('Token not found. Try register once more.');
        }

        $user = $this->userRepository->findOneByEmail($token->getEmail());

        if ($token->isExpired()) {
            $userTokensCount = $this->activationTokenRepository->countUserTokens($user->getEmail());
            if ($userTokensCount > 5) {
                $this->userRepository->remove($user);
                throw new ActivationAttemptsExceedException('Activation attempts exceeded');
            }
            $event = new ActivationTokenExpiredEvent($user->getId()->toString(), $token->getToken());
            $this->eventBus->dispatch($event);
            throw new ActivationTokenExpiredException('Token is expired');
        }
        $user->activate();
        $this->userRepository->save($user);

        $event = new UserActivatedEvent($user->getEmail());
        $this->eventBus->dispatch($event);
    }
}
