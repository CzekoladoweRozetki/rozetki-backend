<?php

declare(strict_types=1);

namespace App\Test\Application\Event;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TestEventHandler
{
    public function __construct(
        private LoggerInterface $logger
    )
    {
    }
    public function __invoke(TestEvent $event): void
    {
        $this->logger->info('Test event executed');
    }

}
