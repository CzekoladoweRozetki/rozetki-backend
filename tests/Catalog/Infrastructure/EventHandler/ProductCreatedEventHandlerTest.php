<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Infrastructure\EventHandler;

use App\Catalog\Domain\Repository\CatalogProductRepository;
use App\Common\Application\Event\EventBus;
use App\Factory\AttributeFactory;
use App\Factory\AttributeValueFactory;
use App\Factory\CategoryFactory;
use App\Product\Domain\Event\Partial\ProductVariant;
use App\Product\Domain\Event\ProductCreatedEvent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

class ProductCreatedEventHandlerTest extends KernelTestCase
{
    use Factories;

    private EventBus $eventBus;
    private CatalogProductRepository $catalogProductRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->eventBus = self::getContainer()->get(EventBus::class);
        $this->catalogProductRepository = self::getContainer()->get(CatalogProductRepository::class);
    }

    public function testShouldCreateCatalogProductWhenProductCreatedEventIsDispatched(): void
    {
        // Arrange
        $productId = Uuid::v4();
        $variantId = Uuid::v4();

        // Create categories for the test
        $category1 = CategoryFactory::createOne();
        $category2 = CategoryFactory::createOne();

        // Create attributes and attribute values
        $colorAttribute = AttributeFactory::createOne([
            'name' => 'Color',
        ]);

        $redValue = AttributeValueFactory::createOne([
            'attribute' => $colorAttribute,
            'value' => 'Red',
        ]);

        $sizeAttribute = AttributeFactory::createOne([
            'name' => 'Size',
        ]);

        $largeValue = AttributeValueFactory::createOne([
            'attribute' => $sizeAttribute,
            'value' => 'Large',
        ]);

        $materialAttribute = AttributeFactory::createOne([
            'name' => 'Fabric',
        ]);
        $metalValue = AttributeValueFactory::createOne([
            'attribute' => $materialAttribute,
            'value' => 'Metal',
        ]);
        $aluminiumValue = AttributeValueFactory::createOne([
            'attribute' => $materialAttribute,
            'value' => 'Aluminium',
        ]);

        $event = new ProductCreatedEvent(
            $productId->toString(),
            'Test Product',
            'Test Description',
            [
                new ProductVariant(
                    $variantId->toString(),
                    'Test Variant',
                    'Test Variant Description',
                    'test-variant',
                    attributeValues: [
                        $redValue->getId()->toString(),
                        $largeValue->getId()->toString(),
                    ]
                ),
            ],
            [$category1->getId()->toString(), $category2->getId()->toString()],
            [$metalValue->getId()->toString(), $aluminiumValue->getId()->toString()]
        );

        // Act
        $this->eventBus->dispatch($event);

        // Assert
        $catalogProduct = $this->catalogProductRepository->findOneById($variantId);
        self::assertNotNull($catalogProduct);
        self::assertEquals('Test Variant', $catalogProduct->getName());
        self::assertEquals('Test Variant Description', $catalogProduct->getDescription());
        self::assertEquals('test-variant', $catalogProduct->getSlug());

        // Check categories
        self::assertArrayHasKey('categories', $catalogProduct->getData());
        self::assertCount(2, $catalogProduct->getData()['categories']);

        $categoryData = $catalogProduct->getData()['categories'];
        $categoryNames = array_column($categoryData, 'name');
        $categorySlugs = array_column($categoryData, 'slug');

        self::assertContains($category1->getName(), $categoryNames);
        self::assertContains($category2->getName(), $categoryNames);
        self::assertContains($category1->getSlug(), $categorySlugs);
        self::assertContains($category2->getSlug(), $categorySlugs);

        // Check attributes
        self::assertArrayHasKey('attributes', $catalogProduct->getData());
        $attributes = $catalogProduct->getData()['attributes'];
        // Check Color attribute
        self::assertArrayHasKey($colorAttribute->getSlug(), $attributes);
        $colorData = $attributes[$colorAttribute->getSlug()];
        self::assertEquals('Color', $colorData['name']);
        self::assertEquals($colorAttribute->getSlug(), $colorData['slug']);
        self::assertEquals($colorAttribute->getId()->toString(), $colorData['id']);

        // Check attribute values
        $attributeValues = $colorData['values'];
        $valueIds = array_map(
            fn ($id) => $id->toString(),
            array_column(
                $attributeValues,
                'id'
            )
        );
        self::assertContains($redValue->getId()->toString(), $valueIds);

        // Check Size attribute
        self::assertArrayHasKey($sizeAttribute->getSlug(), $attributes);
        $sizeData = $attributes[$sizeAttribute->getSlug()];
        self::assertEquals('Size', $sizeData['name']);
        self::assertEquals($sizeAttribute->getSlug(), $sizeData['slug']);
        self::assertEquals($sizeAttribute->getId()->toString(), $sizeData['id']);

        // Check size value
        $attributeValues = $sizeData['values'];
        $valueIds = array_map(
            fn ($id) => $id->toString(),
            array_column(
                $attributeValues,
                'id'
            )
        );
        self::assertContains($largeValue->getId()->toString(), $valueIds);

        // Check Material attribute
        self::assertArrayHasKey($materialAttribute->getSlug(), $attributes);
        $materialData = $attributes[$materialAttribute->getSlug()];
        self::assertEquals('Fabric', $materialData['name']);
        self::assertEquals($materialAttribute->getSlug(), $materialData['slug']);
        self::assertEquals($materialAttribute->getId()->toString(), $materialData['id']);

        // Check material values
        $attributeValues = $materialData['values'];
        $valueIds = array_map(
            fn ($id) => $id->toString(),
            array_column(
                $attributeValues,
                'id'
            )
        );
        self::assertContains($metalValue->getId()->toString(), $valueIds);
        self::assertContains($aluminiumValue->getId()->toString(), $valueIds);
    }
}
