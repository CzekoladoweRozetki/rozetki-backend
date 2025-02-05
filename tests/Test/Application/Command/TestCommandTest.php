<?php

declare(strict_types=1);

namespace App\Tests\Test\Application\Command;

use App\Common\Application\Command\CommandBus;
use App\Test\Application\Command\TestCommand;
use App\Test\Application\Command\TestCommandHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;

class TestCommandTest extends KernelTestCase
{
    public function testDispatch()
    {
        self::bootKernel(['environment' => 'test']);
        $container = self::getContainer();

        $messageBus = $container->get(MessageBusInterface::class);
        $commandBus = new CommandBus($messageBus);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with('Test command executed');

        $handler = new TestCommandHandler($logger);
        $container->set(TestCommandHandler::class, $handler);

        $command = new TestCommand();
        $commandBus->dispatch($command);
    }
}
