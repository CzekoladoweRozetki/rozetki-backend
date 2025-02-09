<?php

declare(strict_types=1);

namespace App\Tests\Auth\Application;

use App\Auth\Application\RegisterUserCommand\RegisterUserCommand;
use App\Auth\Domain\Repository\UserRepository;
use App\Common\Application\Command\CommandBus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class RegisterUserCommandTest extends KernelTestCase
{
    private CommandBus $commandBus;
    private UserRepository $userRepository;

    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->commandBus = $container->get(CommandBus::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->passwordHasher = $container->get(UserPasswordHasherInterface::class);
    }

    public function testRegisterUser(): void
    {
        $command = new RegisterUserCommand(
            id: Uuid::v4(),
            email: 'test@example.com',
            password: 'plainpassword'
        );

        $this->commandBus->dispatch($command);

        $user = $this->userRepository->findOneByEmail('test@example.com');

        $this->assertNotNull($user, 'User was not created.');
        $this->assertEquals('test@example.com', $user->getEmail(), 'User email does not match.');
        $this->assertTrue(
            $this->passwordHasher->isPasswordValid($user, 'plainpassword'),
            'Password was not hashed correctly.'
        );
    }
}
