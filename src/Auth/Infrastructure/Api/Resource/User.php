<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Auth\Infrastructure\Api\DTO\UserInputDTO;
use App\Auth\Infrastructure\Api\Processor\UserPostProcessor;
use App\Auth\Infrastructure\Api\Provider\UserSingleProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(input: UserInputDTO::class, processor: UserPostProcessor::class),
        new Get(provider: UserSingleProvider::class),
    ]
)]
class User
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public string $id,
        #[Assert\Email()]
        public string $email,
    ) {
    }
}
