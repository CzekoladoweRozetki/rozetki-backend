<?php

declare(strict_types=1);

namespace App\Tests\Attribute\Application\Command;

use App\Attribute\Application\Command\CreateAttribute\CreateAttributeCommand;
use App\Attribute\Application\Command\RemoveAttribute\RemoveAttributeCommand;
use App\Attribute\Domain\Exception\GetAttributeByIdQuery\AttributeNotFoundException;
use App\Attribute\Domain\Repository\AttributeRepository;
use App\Common\Application\Command\CommandBus;
use App\Common\Infrastructure\Security\ExecutionContext;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class RemoveAttributeCommandTest extends KernelTestCase
{
    private CommandBus $commandBus;
    private AttributeRepository $attributeRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = static::getContainer()->get(CommandBus::class);
        $this->attributeRepository = static::getContainer()->get(AttributeRepository::class);
    }

    public function testRemoveAttribute(): void
    {
        // Given
        $attributeId = Uuid::v4();
        $createCommand = new CreateAttributeCommand(
            id: $attributeId,
            name: 'Color',
            values: ['Red', 'Green', 'Blue'],
            context: ExecutionContext::Internal
        );
        $this->commandBus->dispatch($createCommand);

        // Verify attribute exists before removal
        $attribute = $this->attributeRepository->findOneById($attributeId);
        $this->assertNotNull($attribute);

        $removeCommand = new RemoveAttributeCommand(
            id: $attributeId,
            executionContext: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($removeCommand);

        // Then
        $attribute = $this->attributeRepository->findOneById($attributeId);
        $this->assertNull($attribute);
    }

    public function testRemoveAttributeWithChildren(): void
    {
        // Given
        $parentId = Uuid::v4();
        $createParentCommand = new CreateAttributeCommand(
            id: $parentId,
            name: 'Features',
            context: ExecutionContext::Internal
        );
        $this->commandBus->dispatch($createParentCommand);

        $childId = Uuid::v4();
        $createChildCommand = new CreateAttributeCommand(
            id: $childId,
            name: 'Color',
            values: ['Red', 'Blue'],
            parentId: $parentId,
            context: ExecutionContext::Internal
        );
        $this->commandBus->dispatch($createChildCommand);

        $removeCommand = new RemoveAttributeCommand(
            id: $parentId,
            executionContext: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($removeCommand);

        // Then
        $parent = $this->attributeRepository->findOneById($parentId);
        $this->assertNull($parent);

        // Child should be also removed
        $child = $this->attributeRepository->findOneById($childId);
        $this->assertNull($child);
    }

    public function testRemoveNonExistentAttribute(): void
    {
        // Given
        $nonExistentId = Uuid::v4();

        $removeCommand = new RemoveAttributeCommand(
            id: $nonExistentId,
            executionContext: ExecutionContext::Internal
        );

        // When & Then
        $this->expectException(AttributeNotFoundException::class);
        try {
            $this->commandBus->dispatch($removeCommand);
        } catch (\Exception $exception) {
            throw $exception->getPrevious() ?? $exception;
        }
        $this->commandBus->dispatch($removeCommand);
    }
}
