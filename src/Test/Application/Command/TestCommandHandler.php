<?php

declare(strict_types=1);

namespace App\Test\Application\Command;

use App\Test\Domain\Entity\Test;
use App\Test\Domain\Repository\TestRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TestCommandHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private TestRepository $testRepository
    ) {
    }

    public function __invoke(TestCommand $command): void
    {
        $test = new Test(
            $command->id,
            $command->name
        );

        $this->testRepository->save($test);

        $this->logger->info('Test command executed');
    }

}
