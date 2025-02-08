<?php

declare(strict_types=1);

namespace App\Test\Application\Command;

use App\Common\Domain\Exception\EntityNotFound;
use App\Test\Domain\Repository\TestRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateTestCommandHandler
{
    public function __construct(
        private TestRepository $testRepository
    ) {
    }

    public function __invoke(UpdateTestCommand $command): void
    {
        $test = $this->testRepository->findOneById($command->id);
        if ($test === null) {
            throw new EntityNotFound();
        }

        $test->setName($command->name);

        $this->testRepository->save($test);
    }

}
