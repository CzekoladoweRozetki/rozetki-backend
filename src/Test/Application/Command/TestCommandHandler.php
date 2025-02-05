<?php

declare(strict_types=1);

namespace App\Test\Application\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TestCommandHandler
{
    public function __construct(
        private LoggerInterface $logger
    )
    {
    }
    public function __invoke(TestCommand $command): void
    {
        $this->logger->info('Test command executed');
    }

}
