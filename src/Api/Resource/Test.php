<?php

declare(strict_types=1);

namespace App\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Provider\TestCollectionProvider;
use App\Api\Provider\TestProvider;

#[ApiResource(
    operations: [
        new Get(provider: TestProvider::class),
        new GetCollection(provider: TestCollectionProvider::class),
        new Post(),
        new Patch(),
        new Delete(),
    ],

)]
class Test
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public string $id,
        public string $name,
    ) {
    }

}
