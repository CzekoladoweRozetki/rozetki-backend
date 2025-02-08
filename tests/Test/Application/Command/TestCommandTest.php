<?php

declare(strict_types=1);

namespace App\Tests\Test\Application\Command;

use App\Common\Application\Command\CommandBus;
use App\Test\Application\Command\TestCommand;
use App\Test\Application\Command\TestCommandHandler;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class TestCommandTest extends KernelTestCase
{
    public function testDispatch()
    {
        self::bootKernel(['environment' => 'test']);
        $container = self::getContainer();

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with('Test command executed');

        $container->set(LoggerInterface::class, $logger);

        $messageBus = $container->get(MessageBusInterface::class);
        $commandBus = new CommandBus($messageBus);


        $handler = $container->get(TestCommandHandler::class);

        $command = new TestCommand(
            id: Uuid::v4(),
            name: 'Test Name'
        );
        $commandBus->dispatch($command);
    }
}
