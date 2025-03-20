<?php

declare(strict_types=1);

namespace App\Tests\Attribute\Application\Query;

use App\Attribute\Application\Query\GetAttributesQuery\AttributeDTO;
use App\Attribute\Application\Query\GetAttributesQuery\GetAttributesQuery;
use App\Common\Application\Query\QueryBus;
use App\Common\Infrastructure\Security\ExecutionContext;
use App\Factory\AttributeFactory;
use App\Factory\AttributeValueFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

class GetAttributesQueryTest extends KernelTestCase
{
    use Factories;

    private QueryBus $queryBus;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->queryBus = static::getContainer()->get(QueryBus::class);
    }

    public function testGetAttributes(): void
    {
        // Given
        // Create some attributes with values
        $attribute1 = AttributeFactory::createOne([
            'name' => 'Color',
        ]);

        $attribute1Values = AttributeValueFactory::createMany(3, [
            'attribute' => $attribute1,
        ]);

        $attribute2 = AttributeFactory::createOne([
            'name' => 'Size',
        ]);

        AttributeValueFactory::createMany(2, [
            'attribute' => $attribute2,
        ]);

        $query = new GetAttributesQuery(null, ExecutionContext::Internal);

        // When
        /** @var AttributeDTO[] $result */
        $result = $this->queryBus->query($query);

        // Then
        $this->assertCount(2, $result);

        // Find the Color attribute in results
        $colorAttribute = null;

        foreach ($result as $attribute) {
            if ('Color' === $attribute->name) {
                $colorAttribute = $attribute;
                break;
            }
        }

        $this->assertNotNull($colorAttribute);
        $this->assertEquals($attribute1->getId()->toString(), $colorAttribute->id);
        $this->assertCount(3, $colorAttribute->values);

        // Verify values contain the expected strings
        $valueTexts = array_map(fn ($value) => $value->value, $colorAttribute->values);
        foreach ($attribute1Values as $attribute1Value) {
            $this->assertContains($attribute1Value->getValue(), $valueTexts);
        }
    }

    public function testGetAttributesWithParentChild(): void
    {
        // Given
        // Create parent attribute
        $parentAttribute = AttributeFactory::createOne([
            'name' => 'Product Features',
        ]);

        // Create child attribute
        $childAttribute = AttributeFactory::createOne([
            'name' => 'Material',
            'parent' => $parentAttribute,
        ]);

        $childValues = AttributeValueFactory::createMany(2, [
            'attribute' => $childAttribute,
        ]);

        $query = new GetAttributesQuery(null, ExecutionContext::Internal);

        // When
        /** @var AttributeDTO[] $result */
        $result = $this->queryBus->query($query);

        // Then
        $this->assertCount(2, $result);

        // Find the parent attribute
        $foundParent = null;
        foreach ($result as $attribute) {
            if ('Product Features' === $attribute->name) {
                $foundParent = $attribute;
                break;
            }
        }

        $this->assertNotNull($foundParent);
        $this->assertEquals($parentAttribute->getId()->toString(), $foundParent->id);
        $this->assertNull($foundParent->parentId);

        // Find the child attribute
        $foundChild = null;
        foreach ($result as $attribute) {
            if ('Material' === $attribute->name) {
                $foundChild = $attribute;
                break;
            }
        }

        $this->assertNotNull($foundChild);
        $this->assertEquals($childAttribute->getId()->toString(), $foundChild->id);
        $this->assertEquals($parentAttribute->getId()->toString(), $foundChild->parentId);
        $this->assertCount(2, $foundChild->values);

        // Verify child values
        $valueTexts = array_map(fn ($value) => $value->value, $foundChild->values);
        foreach ($childValues as $childValue) {
            $this->assertContains($childValue->getValue(), $valueTexts);
        }
    }

    public function testGetAttributesWithNoAttributes(): void
    {
        // Given
        // Clear any attributes created in previous tests
        AttributeFactory::repository()->truncate();

        $query = new GetAttributesQuery([], ExecutionContext::Internal);

        // When
        /** @var AttributeDTO[] $result */
        $result = $this->queryBus->query($query);

        // Then
        $this->assertCount(0, $result);
        $this->assertEmpty($result);
    }
}
