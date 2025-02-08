<?php

declare(strict_types=1);

namespace App\Test\Infrastructure\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Common\Application\Query\QueryBus;
use App\Test\Application\Query\GetAllTestQuery;
use App\Test\Application\Query\TestDTO;
use App\Test\Infrastructure\Api\Resource\Test;

/**
 * @implements ProviderInterface<Test>
 */
class TestCollectionProvider implements ProviderInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $query = new GetAllTestQuery();

        $tests = $this->queryBus->query($query);

        return array_map(
            fn (TestDTO $test) => new Test($test->id->toString(), $test->name),
            $tests
        );
    }
}
