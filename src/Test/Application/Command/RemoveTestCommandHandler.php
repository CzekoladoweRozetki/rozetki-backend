<?php

declare(strict_types=1);

namespace App\Test\Application\Command;

use App\Common\Domain\Exception\EntityNotFound;
use App\Test\Domain\Repository\TestRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RemoveTestCommandHandler
{
    public function __construct(
        private TestRepository $testRepository
    ) {
    }

    public function __invoke(RemoveTestCommand $command): void
    {
        $test = $this->testRepository->findOneById($command->id);
        if ($test === null) {
            throw new EntityNotFound();
        }

        $this->testRepository->remove($test);
    }

}
