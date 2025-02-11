<?php

declare(strict_types=1);

namespace App\Test\Infrastructure\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Test\Infrastructure\Api\Resource\ProtectedTest;

/**
 * @implements ProviderInterface<ProtectedTest>
 */
class ProtectedTestCollectionProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return [
            new ProtectedTest('test1'),
            new ProtectedTest('test2'),
        ];
    }
}
