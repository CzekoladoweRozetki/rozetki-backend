<?php

declare(strict_types=1);

namespace App\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Resource\Test;
use App\Common\Application\Query\QueryBus;
use App\Test\Application\Query\GetAllTestQuery;
use App\Test\Application\Query\TestDTO;

class TestCollectionProvider implements ProviderInterface
{
    public function __construct(
        private QueryBus $queryBus
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $query = new GetAllTestQuery();

        $tests = $this->queryBus->query($query);

        return array_map(
            fn(TestDTO $test) => new Test($test->id->toString(), $test->name),
            $tests
        );
    }
}
