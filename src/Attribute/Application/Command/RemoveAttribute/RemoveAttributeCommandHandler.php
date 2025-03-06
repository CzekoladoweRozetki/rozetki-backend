<?php

declare(strict_types=1);

namespace App\Attribute\Application\Command\RemoveAttribute;

use App\Attribute\Domain\Exception\GetAttributeByIdQuery\AttributeNotFoundException;
use App\Attribute\Domain\Repository\AttributeRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RemoveAttributeCommandHandler
{
    public function __construct(
        private AttributeRepository $attributeRepository,
    ) {
    }

    public function __invoke(RemoveAttributeCommand $command): void
    {
        $attribute = $this->attributeRepository->findOneById($command->id);

        if (!$attribute) {
            throw new AttributeNotFoundException('Attribute not found');
        }

        $this->attributeRepository->remove($attribute);
    }
}
