<?php

declare(strict_types=1);

namespace App\Tests\Attribute\Application\Command;

use App\Attribute\Application\Command\CreateAttribute\CreateAttributeCommand;
use App\Attribute\Domain\Entity\Attribute;
use App\Attribute\Domain\Entity\AttributeValue;
use App\Attribute\Domain\Repository\AttributeRepository;
use App\Common\Application\Command\CommandBus;
use App\Common\Infrastructure\Security\ExecutionContext;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class CreateAttributeCommandTest extends KernelTestCase
{
    private CommandBus $commandBus;
    private AttributeRepository $attributeRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = static::getContainer()->get(CommandBus::class);
        $this->attributeRepository = static::getContainer()->get(AttributeRepository::class);
    }

    public function testCreateAttribute(): void
    {
        // Given
        $attributeId = Uuid::v4();
        $attributeName = 'Color';
        $attributeValues = ['Red', 'Green', 'Blue'];

        $command = new CreateAttributeCommand(
            id: $attributeId,
            name: $attributeName,
            values: $attributeValues,
            context: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $attribute = $this->attributeRepository->findOneById($attributeId);

        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertEquals($attributeName, $attribute->getName());
        $this->assertNull($attribute->getParent());
        $this->assertAttributeValuesMatch($attributeValues, $attribute->getValues());
    }

    public function testCreateAttributeWithParent(): void
    {
        // Given
        $parentId = Uuid::v4();
        $parentCommand = new CreateAttributeCommand(
            id: $parentId,
            name: 'Product Features',
            context: ExecutionContext::Internal
        );
        $this->commandBus->dispatch($parentCommand);

        $attributeId = Uuid::v4();
        $attributeName = 'Size';
        $attributeValues = ['Small', 'Medium', 'Large'];

        $command = new CreateAttributeCommand(
            id: $attributeId,
            name: $attributeName,
            values: $attributeValues,
            parentId: $parentId,
            context: ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $attribute = $this->attributeRepository->findOneById($attributeId);

        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertEquals($attributeName, $attribute->getName());
        $this->assertNotNull($attribute->getParent());
        $this->assertEquals($parentId, $attribute->getParent()->getId());
        $this->assertAttributeValuesMatch($attributeValues, $attribute->getValues());
    }

    /**
     * @param array<mixed>                    $expectedValues
     * @param Collection<int, AttributeValue> $actualValues
     */
    private function assertAttributeValuesMatch(array $expectedValues, Collection $actualValues): void
    {
        $this->assertCount(count($expectedValues), $actualValues);

        $actualValuesList = [];
        foreach ($actualValues as $attributeValue) {
            $actualValuesList[] = $attributeValue->getValue();
        }

        foreach ($expectedValues as $expectedValue) {
            $this->assertContains($expectedValue, $actualValuesList);
        }
    }
}
