<?php

declare(strict_types=1);

namespace App\Test\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Test\Infrastructure\Api\DTO\Test\TestInputDTO;
use App\Test\Infrastructure\Api\Processor\Test\TessCreateProcessor;
use App\Test\Infrastructure\Api\Processor\Test\TestDeleteProcessor;
use App\Test\Infrastructure\Api\Processor\Test\TestUpdateProcessor;
use App\Test\Infrastructure\Api\Provider\TestCollectionProvider;
use App\Test\Infrastructure\Api\Provider\TestProvider;

#[ApiResource(
    operations: [
        new Get(provider: TestProvider::class),
        new GetCollection(provider: TestCollectionProvider::class),
        new Post(input: TestInputDTO::class, processor: TessCreateProcessor::class),
        new Patch(provider: TestProvider::class, processor: TestUpdateProcessor::class),
        new Delete(processor: TestDeleteProcessor::class, provider: TestProvider::class),
    ],

)]
class Test
{
    public function __construct(
        #[ApiProperty(identifier: true,)]
        public string $id,
        public string $name,
    ) {
    }

}
