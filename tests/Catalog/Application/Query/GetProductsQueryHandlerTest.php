<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Application\Query;

use App\Catalog\Application\Query\GetProducts\GetProductsQuery;
use App\Common\Application\Query\QueryBus;
use App\Factory\CatalogProductFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

/**
 * @covers \App\Catalog\Application\Query\GetProducts\GetProductsQueryHandler
 * @covers \App\Catalog\Application\Query\GetProducts\GetProductsQuery
 */
class GetProductsQueryHandlerTest extends KernelTestCase
{
    use Factories;

    private QueryBus $queryBus;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->queryBus = self::getContainer()->get(QueryBus::class);
    }

    public function testShouldReturnMatchingProducts(): void
    {
        // Given
        CatalogProductFactory::createSequence([
            [
                'id' => Uuid::v4(),
                'name' => 'First Product',
                'description' => 'Description 1',
                'slug' => 'first-product',
            ],
            [
                'id' => Uuid::v4(),
                'name' => 'Second Product',
                'description' => 'Description 2',
                'slug' => 'second-product',
            ],
            [
                'id' => Uuid::v4(),
                'name' => 'Different Item',
                'description' => 'Description 3',
                'slug' => 'different-item',
            ],
        ]);

        $query = new GetProductsQuery(
            search: 'Product',
            page: 1,
            limit: 10
        );

        // When
        $result = $this->queryBus->query($query);

        // Then
        self::assertCount(2, $result);
        self::assertEquals('First Product', $result[0]->name);
        self::assertEquals('Second Product', $result[1]->name);
    }

    public function testShouldReturnEmptyArrayWhenNoMatches(): void
    {
        // Given
        CatalogProductFactory::createSequence([
            [
                'id' => Uuid::v4(),
                'name' => 'First Product',
                'description' => 'Description 1',
                'slug' => 'first-product',
            ],
            [
                'id' => Uuid::v4(),
                'name' => 'Second Product',
                'description' => 'Description 2',
                'slug' => 'second-product',
            ],
        ]);

        $query = new GetProductsQuery(
            search: 'NonExistent',
            page: 1,
            limit: 10
        );

        // When
        $result = $this->queryBus->query($query);

        // Then
        self::assertEmpty($result);
    }

    public function testShouldPaginateResults(): void
    {
        // Given
        CatalogProductFactory::createSequence([
            ['id' => Uuid::v4(), 'name' => 'Product 1', 'description' => 'Description 1', 'slug' => 'product-1'],
            ['id' => Uuid::v4(), 'name' => 'Product 2', 'description' => 'Description 2', 'slug' => 'product-2'],
            ['id' => Uuid::v4(), 'name' => 'Product 3', 'description' => 'Description 3', 'slug' => 'product-3'],
        ]);

        $query = new GetProductsQuery(
            search: 'Product',
            page: 2,
            limit: 2
        );

        // When
        $result = $this->queryBus->query($query);

        // Then
        self::assertCount(1, $result);
        self::assertEquals('Product 3', $result[0]->name);
    }
}
