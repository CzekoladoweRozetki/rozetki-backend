<?php

declare(strict_types=1);

namespace App\Attribute\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Attribute\Infrastructure\Api\DTO\AttributeInputDTO;
use App\Attribute\Infrastructure\Api\Processor\AttributePostProcessor;

#[ApiResource(
    operations: [
        new Post(input: AttributeInputDTO::class, processor: AttributePostProcessor::class),
    ]
)]
class Attribute
{
    /**
     * @param array<int, string> $values
     */
    public function __construct(
        #[ApiProperty(identifier: true)]
        public string $id,
        public string $name,
        public array $values = [],
        public ?string $parentId = null,
    ) {
    }
}
