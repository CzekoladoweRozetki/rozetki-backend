<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\RequestPasswordReset;

use App\Auth\Domain\Entity\PasswordResetToken;
use App\Auth\Domain\Event\PasswordResetRequested;
use App\Auth\Domain\Repository\PasswordResetTokenRepository;
use App\Auth\Domain\Repository\UserRepository;
use App\Common\Application\Event\EventBus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
class RequestPasswordResetCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordResetTokenRepository $passwordResetTokenRepository,
        private EventBus $eventBus,
    ) {
    }

    public function __invoke(RequestPasswordResetCommand $command): void
    {
        $token = Uuid::v4();

        $user = $this->userRepository->findOneByEmail($command->email);

        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        $passwordResetToken = new PasswordResetToken($token, new \DateTimeImmutable('tomorrow'), $user);
        $this->passwordResetTokenRepository->save($passwordResetToken);

        $event = new PasswordResetRequested($user->getId(), $user->getEmail(), $token);
        $this->eventBus->dispatch($event);
    }
}
