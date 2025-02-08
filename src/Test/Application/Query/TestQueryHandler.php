<?php

declare(strict_types=1);

namespace App\Test\Application\Query;

use App\Test\Domain\Repository\TestRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TestQueryHandler
{
    public function __construct(
        private TestRepository $testRepository,
    ) {
    }

    public function __invoke(TestQuery $query): TestDTO
    {
        $test = $this->testRepository->findOneById($query->id);

        return new TestDTO($test->getId(), $test->getName());
    }
}
