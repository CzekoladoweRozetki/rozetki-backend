<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Infrastructure\EventHandler;

use App\Catalog\Domain\Repository\CatalogProductRepository;
use App\Common\Application\Event\EventBus;
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
        $event = new ProductCreatedEvent(
            $productId->toString(),
            'Test Product',
            'Test Description',
            [
                new ProductVariant(
                    $variantId->toString(),
                    'Test Variant',
                    'Test Variant Description',
                    'test-variant'
                ),
            ]
        );

        // Act
        $this->eventBus->dispatch($event);

        // Assert
        $catalogProduct = $this->catalogProductRepository->findOneById($variantId);
        self::assertNotNull($catalogProduct);
        self::assertEquals('Test Variant', $catalogProduct->getName());
        self::assertEquals('Test Variant Description', $catalogProduct->getDescription());
        self::assertEquals('test-variant', $catalogProduct->getSlug());
    }
}
