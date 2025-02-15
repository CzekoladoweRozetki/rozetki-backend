<?php

declare(strict_types=1);

namespace App\Tests\Test\Application\Event;

use App\Common\Application\Event\EventBus;
use App\Test\Application\Event\TestEvent;
use App\Test\Application\Event\TestEventHandler;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class TestEventTest extends KernelTestCase
{
    public function testDispatch(): void
    {
        self::bootKernel(['environment' => 'test']);
        $container = self::getContainer();

        $messageBus = $container->get(MessageBusInterface::class);
        $eventBus = new EventBus($messageBus);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with('Test event executed');

        $handler = new TestEventHandler($logger);
        $container->set(TestEventHandler::class, $handler);

        $command = new TestEvent();
        $eventBus->dispatch($command);
    }
}
