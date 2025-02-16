<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Auth\Infrastructure\Api\Processor\PasswordResetRequestProcessor;

#[ApiResource(
    operations: [
        new Post(processor: PasswordResetRequestProcessor::class, status: 204),
    ]
)]
class PasswordResetRequest
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public string $email,
    ) {
    }

}
