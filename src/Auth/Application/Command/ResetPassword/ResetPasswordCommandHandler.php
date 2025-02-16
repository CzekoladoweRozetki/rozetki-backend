<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\ResetPassword;

use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Exception\PasswordResetTokenExpiredException;
use App\Auth\Domain\Exception\PasswordResetTokenNotFoundException;
use App\Auth\Domain\Repository\PasswordResetTokenRepository;
use App\Auth\Domain\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[AsMessageHandler]
class ResetPasswordCommandHandler
{
    public function __construct(
        private PasswordResetTokenRepository $passwordResetTokenRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(ResetPasswordCommand $command): void
    {
        $passwordResetToken = $this->passwordResetTokenRepository->findOneByToken($command->token);

        if (!$passwordResetToken) {
            throw new PasswordResetTokenNotFoundException();
        }

        if ($passwordResetToken->isExpired()) {
            throw new PasswordResetTokenExpiredException();
        }

        /**
         * @var User&PasswordAuthenticatedUserInterface $user
         */
        $user = $passwordResetToken->getUser();

        // hash password
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $command->newPassword);

        $user->setPassword($hashedPassword);
        $this->userRepository->save($user);
        $this->passwordResetTokenRepository->remove($passwordResetToken);
    }
}
