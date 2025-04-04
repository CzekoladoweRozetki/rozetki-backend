<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Category\Infrastructure\Api\DTO\CategoryInputDTO;
use App\Category\Infrastructure\Api\Processor\CategoryPostProcessor;
use App\Category\Infrastructure\Api\Processor\CategoryPutProcessor;
use App\Category\Infrastructure\Api\Processor\CategoryRemoveProcessor;
use App\Category\Infrastructure\Api\Provider\CategoryCollectionProvider;
use App\Category\Infrastructure\Api\Provider\CategorySingleProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(input: CategoryInputDTO::class, processor: CategoryPostProcessor::class),
        new Get(provider: CategorySingleProvider::class),
        new GetCollection(provider: CategoryCollectionProvider::class),
        new Put(provider: CategorySingleProvider::class, processor: CategoryPutProcessor::class),
        new Delete(provider: CategorySingleProvider::class, processor: CategoryRemoveProcessor::class),
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
