<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Infrastructure\Api\Resource;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Factory\CatalogProductFactory;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

/**
 * @covers \App\Catalog\Infrastructure\Api\Resource\CatalogProduct
 * @covers \App\Catalog\Infrastructure\Api\Provider\CatalogProductCollectionProvider
 * @covers \App\Catalog\Infrastructure\Api\Provider\CatalogProductSingleProvider
 */
class CatalogProductTest extends ApiTestCase
{
    use Factories;

    private const API_URL = '/api/catalog_products';

    private Client $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    public function testShouldGetCatalogProductBySlug(): void
    {
        // Given
        $product = CatalogProductFactory::createOne([
            'id' => Uuid::v4(),
            'name' => 'Test Product',
            'description' => 'Test Description',
            'slug' => 'test-product',
        ]);

        // When
        $response = $this->client->request('GET', self::API_URL.'/test-product');
        $content = $response->toArray();

        // Then
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/api/contexts/CatalogProduct',
            '@type' => 'CatalogProduct',
            'name' => 'Test Product',
            'description' => 'Test Description',
            'id' => 'test-product',
        ]);
    }

    public function testShouldReturn404WhenProductNotFound(): void
    {
        // When
        $this->client->request('GET', self::API_URL.'/non-existent');

        // Then
        self::assertResponseStatusCodeSame(404);
    }

    public function testShouldReturnCollectionOfProducts(): void
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

        // When
        $response = $this->client->request('GET', self::API_URL);
        $content = $response->toArray();

        // Then
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/api/contexts/CatalogProduct',
            'totalItems' => 2,
        ]);
        self::assertCount(2, $content['member']);
    }

    public function testShouldFilterProductsBySearchTerm(): void
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
                'name' => 'Different Item',
                'description' => 'Description 2',
                'slug' => 'different-item',
            ],
        ]);

        // When
        $response = $this->client->request('GET', self::API_URL.'?search=Product');
        $content = $response->toArray();

        // Then
        self::assertResponseIsSuccessful();
        self::assertCount(1, $content['member']);
        self::assertEquals('First Product', $content['member'][0]['name']);
    }

    public function testShouldPaginateProducts(): void
    {
        // Given
        CatalogProductFactory::createSequence([
            [
                'id' => Uuid::v4(),
                'name' => 'Product 1',
                'description' => 'Description 1',
                'slug' => 'product-1',
            ],
            [
                'id' => Uuid::v4(),
                'name' => 'Product 2',
                'description' => 'Description 2',
                'slug' => 'product-2',
            ],
            [
                'id' => Uuid::v4(),
                'name' => 'Product 3',
                'description' => 'Description 3',
                'slug' => 'product-3',
            ],
        ]);

        // When
        $response = $this->client->request('GET', self::API_URL.'?page=2&itemsPerPage=2');
        $content = $response->toArray();

        // Then
        self::assertResponseIsSuccessful();
        self::assertCount(1, $content['member']);
        self::assertEquals('Product 3', $content['member'][0]['name']);
    }
}
