<?php

declare(strict_types=1);

namespace App\Attribute\Application\Command\CreateAttribute;

use App\Attribute\Domain\Entity\Attribute;
use App\Attribute\Domain\Repository\AttributeRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateAttributeCommandHandler
{
    public function __construct(
        private AttributeRepository $attributeRepository,
    ) {
    }

    public function __invoke(CreateAttributeCommand $command): void
    {
        $parent = $command->parentId ? $this->attributeRepository->findOneById($command->parentId) : null;

        $attribute = new Attribute(
            id: $command->id,
            name: $command->name,
            parent: $parent
        );

        foreach ($command->values as $value) {
            $attribute->addValue($value);
        }

        $this->attributeRepository->save($attribute);
    }
}
