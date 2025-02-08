<?php

declare(strict_types=1);

namespace App\Test\Application\Query;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
class GetAllTestQueryHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array<TestDTO>
     */
    public function __invoke(GetAllTestQuery $query): array
    {
        $conn = $this->entityManager->getConnection();
        $sql = 'SELECT * FROM test';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        $tests = array_map(fn ($test) => new TestDTO(Uuid::fromString($test['id']), $test['name']),
            $result->fetchAllAssociative());

        return $tests;
    }
}
