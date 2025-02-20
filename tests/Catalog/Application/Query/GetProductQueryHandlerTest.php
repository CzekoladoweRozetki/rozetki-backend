<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Application\Query;

use App\Catalog\Application\Query\GetProduct\GetProductQuery;
use App\Common\Application\Query\QueryBus;
use App\Factory\CatalogProductFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

/**
 * @covers \App\Catalog\Application\Query\GetProduct\GetProductQueryHandler
 * @covers \App\Catalog\Application\Query\GetProduct\GetProductQuery
 */
class GetProductQueryHandlerTest extends KernelTestCase
{
    use Factories;

    private QueryBus $queryBus;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->queryBus = self::getContainer()->get(QueryBus::class);
    }

    public function testShouldReturnProductBySlug(): void
    {
        // Arrange
        $product = CatalogProductFactory::createOne([
            'id' => Uuid::v4(),
            'name' => 'Test Product',
            'description' => 'Test Description',
            'slug' => 'test-product',
            'data' => ['key' => 'value'],
        ]);

        $query = new GetProductQuery('test-product');

        // Act
        $result = $this->queryBus->query($query);

        // Assert
        self::assertIsArray($result);
        self::assertEquals($product->getId()->__toString(), $result['id']);
        self::assertEquals('Test Product', $result['name']);
        self::assertEquals('Test Description', $result['description']);
        self::assertEquals('test-product', $result['slug']);
    }

    public function testShouldReturnFalseWhenProductNotFound(): void
    {
        // Arrange
        $query = new GetProductQuery('non-existent-product');

        // Act
        $result = $this->queryBus->query($query);

        // Assert
        self::assertNull($result);
    }
}
