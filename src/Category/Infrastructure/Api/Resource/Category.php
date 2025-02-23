<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Category\Infrastructure\Api\DTO\CategoryInputDTO;
use App\Category\Infrastructure\Api\Processor\CategoryPostProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(input: CategoryInputDTO::class, processor: CategoryPostProcessor::class),
    ]
)]
class Category
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public string $id,
        #[Assert\NotBlank(message: 'Name should not be blank')]
        public string $name,
        public string $slug,
        public ?string $parent = null,
    ) {
    }
}
