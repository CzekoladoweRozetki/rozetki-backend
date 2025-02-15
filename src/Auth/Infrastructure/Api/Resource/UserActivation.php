<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Auth\Infrastructure\Api\Processor\UserActivationProcessor;

#[ApiResource(
    operations: [
        new Post(processor: UserActivationProcessor::class, status: 204),
    ]
)]
class UserActivation
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public string $token,
    ) {
    }
}
