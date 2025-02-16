<?php

namespace App\Tests\Auth\Application\Command;

use App\Auth\Application\Command\RequestPasswordReset\RequestPasswordResetCommand;
use App\Auth\Domain\Repository\PasswordResetTokenRepository;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Zenstruck\Foundry\Test\Factories;

/**
 * @covers \App\Auth\Application\Command\RequestPasswordReset\RequestPasswordResetCommand
 * @covers \App\Auth\Application\Command\RequestPasswordReset\RequestPasswordResetCommandHandler
 */
class RequestPasswordResetCommandTest extends WebTestCase
{
    use Factories;

    private MessageBusInterface $commandBus;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = self::getContainer()->get(MessageBusInterface::class);
    }

    public function testRequestPasswordReset(): void
    {
        // Given
        $user = UserFactory::createOne();
        $email = $user->getEmail();
        /**
         * @var PasswordResetTokenRepository $tokenRepository
         */
        $tokenRepository = self::getContainer()->get(PasswordResetTokenRepository::class);

        // When
        $command = new RequestPasswordResetCommand($email);
        $this->commandBus->dispatch($command);

        $token = $tokenRepository->findOneByUser($user);

        // Then
        $this->assertEquals($user, $token->getUser());
        $this->assertFalse($token->isExpired());
    }
}
