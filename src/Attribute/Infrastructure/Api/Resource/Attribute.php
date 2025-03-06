<?php

declare(strict_types=1);

namespace App\Attribute\Infrastructure\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Attribute\Infrastructure\Api\DTO\AttributeInputDTO;
use App\Attribute\Infrastructure\Api\Processor\AttributeDeleteProcessor;
use App\Attribute\Infrastructure\Api\Processor\AttributePostProcessor;
use App\Attribute\Infrastructure\Api\Provider\AttributeSingleProvider;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Post(input: AttributeInputDTO::class, processor: AttributePostProcessor::class),
        new Get(provider: AttributeSingleProvider::class),
        new Delete(provider: AttributeSingleProvider::class, processor: AttributeDeleteProcessor::class),
    ], normalizationContext: ['groups' => ['attribute']]
)]
class Attribute
{
    /**
     * @param array<int, AttributeValue> $values
     */
    public function __construct(
        #[ApiProperty(identifier: true)]
        #[Groups(['attribute'])]
        public string $id,
        #[Groups(['attribute'])]
        public string $name,
        #[Groups(['attribute'])]
        public array $values = [],
        #[Groups(['attribute'])]
        public ?string $parentId = null,
    ) {
    }
}
