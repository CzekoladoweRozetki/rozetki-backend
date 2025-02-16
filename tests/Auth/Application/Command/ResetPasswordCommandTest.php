<?php

declare(strict_types=1);

namespace App\Tests\Auth\Application\Command;

use App\Auth\Application\Command\ResetPassword\ResetPasswordCommand;
use App\Auth\Domain\Entity\PasswordResetToken;
use App\Auth\Domain\Exception\PasswordResetTokenExpiredException;
use App\Auth\Domain\Exception\PasswordResetTokenNotFoundException;
use App\Auth\Domain\Repository\PasswordResetTokenRepository;
use App\Auth\Domain\Repository\UserRepository;
use App\Common\Application\Command\CommandBus;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

/**
 * @covers \App\Auth\Application\Command\ResetPassword\ResetPasswordCommand
 * @covers \App\Auth\Application\Command\ResetPassword\ResetPasswordCommandHandler
 */
class ResetPasswordCommandTest extends WebTestCase
{
    use Factories;

    private CommandBus $commandBus;
    private UserRepository $userRepository;
    private PasswordResetTokenRepository $passwordResetTokenRepository;

    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = self::getContainer()->get(CommandBus::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
        $this->passwordResetTokenRepository = self::getContainer()->get(PasswordResetTokenRepository::class);
        $this->passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
    }

    public function testResetPassword(): void
    {
        // Given
        $user = UserFactory::createOne([
            'email' => 'user@example.com'
        ]);

        $token = Uuid::v4();
        $passwordResetToken = new PasswordResetToken($token, new \DateTimeImmutable('tomorrow'), $user);
        $this->passwordResetTokenRepository->save($passwordResetToken);

        // When
        $command = new ResetPasswordCommand($token->toString(), 'newPassword');
        $this->commandBus->dispatch($command);

        // Then
        $updatedUser = $this->userRepository->findOneByEmail('user@example.com');
        $this->assertNotNull($updatedUser);
        $this->assertTrue($this->passwordHasher->isPasswordValid($updatedUser, 'newPassword'));

        $this->assertNull($this->passwordResetTokenRepository->findOneByToken($token->toString()));
    }

    public function testResetPasswordTokenNotFound(): void
    {
        // Given
        $token = Uuid::v4()->toString();

        // When
        $command = new ResetPasswordCommand($token, 'newPassword');
        $this->expectException(HandlerFailedException::class);
        $this->expectException(PasswordResetTokenNotFoundException::class);
        try {
            $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }
    }

    public function testResetPasswordTokenExpired(): void
    {
        // Given
        $user = UserFactory::createOne([
            'email' => 'user@example.com'
        ]);

        $token = Uuid::v4();
        $expiredToken = new PasswordResetToken($token, new \DateTimeImmutable('yesterday'), $user);
        $this->passwordResetTokenRepository->save($expiredToken);

        // When
        $command = new ResetPasswordCommand($token->toString(), 'newPassword');
        $this->expectException(HandlerFailedException::class);
        $this->expectException(PasswordResetTokenExpiredException::class);
        try {
            $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }
    }
}
