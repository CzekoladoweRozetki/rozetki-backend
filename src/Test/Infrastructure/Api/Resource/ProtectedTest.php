<?php

declare(strict_types=1);

namespace App\Test\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Test\Infrastructure\Api\Provider\ProtectedTestCollectionProvider;

#[ApiResource(
    operations: [
        new GetCollection(provider: ProtectedTestCollectionProvider::class),
    ]
)]
readonly class ProtectedTest
{
    public function __construct(
        public string $name,
    ) {
    }
}
