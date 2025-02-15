<?php

declare(strict_types=1);

namespace App\Tests\Auth\Application\Command;

use App\Auth\Application\Command\ActivateUser\ActivateUserCommand;
use App\Auth\Domain\Entity\ActivationToken;
use App\Auth\Domain\Repository\ActivationTokenRepository;
use App\Auth\Domain\Repository\UserRepository;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Zenstruck\Foundry\Test\Factories;

class ActivateUserCommandTest extends KernelTestCase
{
    use Factories;

    private MessageBusInterface $commandBus;
    private ActivationTokenRepository $activationTokenRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->commandBus = $container->get(MessageBusInterface::class);
        $this->activationTokenRepository = $container->get(ActivationTokenRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
    }

    public function testActivateUser(): void
    {
        // Given
        $user = UserFactory::createOne();
        $activationToken = ActivationToken::create($user->getEmail());
        $this->activationTokenRepository->save($activationToken);

        // When
        $command = new ActivateUserCommand($activationToken->getToken());
        $this->commandBus->dispatch($command);

        // Then
        $activatedUser = $this->userRepository->findOneByEmail($user->getEmail());
        $this->assertTrue($activatedUser->isActive(), 'User was not activated.');
    }
}
