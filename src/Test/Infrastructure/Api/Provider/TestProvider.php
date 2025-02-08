<?php

declare(strict_types=1);

namespace App\Test\Infrastructure\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Common\Application\Query\QueryBus;
use App\Test\Application\Query\TestQuery;
use App\Test\Infrastructure\Api\Resource\Test;
use Symfony\Component\Uid\Uuid;

class TestProvider implements ProviderInterface
{
    public function __construct(
        private QueryBus $queryBus
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $query = new TestQuery(Uuid::fromString($uriVariables['id']));

        $test = $this->queryBus->query($query);

        return new Test($test->id->toString(), $test->name);
    }
}
