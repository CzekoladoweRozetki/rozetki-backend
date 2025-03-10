<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Auth\Infrastructure\Api\Processor\PasswordResetProcessor;

#[ApiResource(
    operations: [
        new Post(status: 204, processor: PasswordResetProcessor::class),
    ]
)]
class PasswordReset
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public string $token,
        public string $newPassword,
    ) {
    }
}
