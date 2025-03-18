<?php

declare(strict_types=1);

namespace App\Tests\Product\Application\Command;

use App\Common\Application\Event\EventBus;
use App\Common\Infrastructure\Security\ExecutionContext;
use App\Factory\AttributeFactory;
use App\Factory\AttributeValueFactory;
use App\Factory\CategoryFactory;
use App\Product\Application\Command\CreateProduct\CreateProductCommand;
use App\Product\Application\Command\CreateProduct\ProductVariantDTO;
use App\Product\Domain\Event\ProductCreatedEvent;
use App\Product\Domain\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class CreateProductCommandTest extends KernelTestCase
{
    private MessageBusInterface $commandBus;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = self::getContainer()->get(MessageBusInterface::class);
    }

    public function testCreateProduct(): void
    {
        $command = new CreateProductCommand(
            Uuid::v4(),
            'Test Product',
            'This is a test product description.',
            executionContext: ExecutionContext::Internal
        );

        $this->commandBus->dispatch($command);

        // Add assertions to verify the product was created
        // For example, you can check the database or the repository
        $productRepository = self::getContainer()->get(ProductRepository::class);
        $product = $productRepository->find($command->id);

        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals('This is a test product description.', $product->getDescription());
    }

    public function testCreateProductWithVariants(): void
    {
        // Given
        $productId = Uuid::v4();
        $command = new CreateProductCommand(
            $productId,
            'Test Product',
            'This is a test product description.',
            [
                new ProductVariantDTO('Variant 1', 'First variant description'),
                new ProductVariantDTO('Variant 2', 'Second variant description'),
            ],
            executionContext: ExecutionContext::Internal
        );

        // Mock the EventBus
        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->callback(function (ProductCreatedEvent $event) use ($productId) {
                    return $event->getProductId() === $productId->toString()
                        && 'Test Product' === $event->getName()
                        && 'This is a test product description.' === $event->getDescription()
                        && 2 === count($event->getVariants())
                        && 'Variant 1' === $event->getVariants()[0]->name
                        && 'First variant description' === $event->getVariants()[0]->description
                        && 'Variant 2' === $event->getVariants()[1]->name
                        && 'Second variant description' === $event->getVariants()[1]->description;
                })
            );

        // Replace the EventBus in the container with the mock
        self::getContainer()->set(EventBus::class, $eventBus);

        // When
        $this->commandBus->dispatch($command);

        // Then
        $productRepository = self::getContainer()->get(ProductRepository::class);
        $product = $productRepository->find($productId);

        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals('This is a test product description.', $product->getDescription());

        $variants = $product->getVariants();
        $this->assertCount(2, $variants);

        $variantsArray = $variants->toArray();
        $this->assertEquals('Variant 1', $variantsArray[0]->getName());
        $this->assertEquals('First variant description', $variantsArray[0]->getDescription());
        $this->assertEquals('Variant 2', $variantsArray[1]->getName());
        $this->assertEquals('Second variant description', $variantsArray[1]->getDescription());
    }

    public function testCreateProductWithCategories(): void
    {
        // Given
        $productId = Uuid::v4();
        $category1 = CategoryFactory::createOne([
            'name' => 'Category 1',
            'slug' => 'category-1',
        ]);
        $categoryId2 = Uuid::v4()->toString();
        $category2 = CategoryFactory::createOne([
            'name' => 'Category 2',
            'slug' => 'category-2',
        ]);

        $command = new CreateProductCommand(
            $productId,
            'Test Product With Categories',
            'This is a product with categories',
            [],
            [$category1->getId()->toString(), $category2->getId()->toString()],
            [],
            ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $productRepository = self::getContainer()->get(ProductRepository::class);
        $product = $productRepository->find($productId);

        $this->assertNotNull($product);
        $this->assertEquals('Test Product With Categories', $product->getName());
        $this->assertEquals('This is a product with categories', $product->getDescription());

        $categories = $product->getCategories();
        $this->assertCount(2, $categories);
        $this->assertContains($category1->getId()->toString(), $categories);
        $this->assertContains($category2->getId()->toString(), $categories);
    }

    public function testCreateProductWithAttributes(): void
    {
        // Given
        $productId = Uuid::v4();

        // Create test attributes and values
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

        $command = new CreateProductCommand(
            $productId,
            'Test Product With Attributes',
            'This is a product with attribute values',
            [],
            [],
            [
                $redValue->getId(),
                $largeValue->getId(),
            ],
            ExecutionContext::Internal
        );

        // When
        $this->commandBus->dispatch($command);

        // Then
        $productRepository = self::getContainer()->get(ProductRepository::class);
        $product = $productRepository->find($productId);

        $this->assertNotNull($product);
        $this->assertEquals('Test Product With Attributes', $product->getName());

        $attributes = $product->getAttributes();
        $this->assertCount(2, $attributes);

        // Check that both attribute values are associated with the product
        $attributeValueIds = array_map(
            fn ($attr) => $attr->getAttributeValueId()->toString(),
            $attributes->toArray()
        );

        $this->assertContains($redValue->getId()->toString(), $attributeValueIds);
        $this->assertContains($largeValue->getId()->toString(), $attributeValueIds);
    }
}
