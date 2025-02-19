<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Infrastructure\Api\Resource;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Factory\CatalogProductFactory;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

class CatalogProductTest extends ApiTestCase
{
    use Factories;

    private const API_URL = '/api/catalog_products/';

    private Client $client;

    public function setUp(): void
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
        $client = $this->client;
        $url = self::API_URL.$product->getSlug();
        $response = $client->request('GET', $url);
        $content = $response->toArray();

        // Then
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/api/contexts/CatalogProduct',
            '@type' => 'CatalogProduct',
            'uuid' => $product->getId()->__toString(),
            'name' => 'Test Product',
            'description' => 'Test Description',
            'id' => 'test-product',
        ]);
    }

    public function testShouldReturn404WhenProductNotFound(): void
    {
        // Given
        static::createClient()->request('GET', self::API_URL.'/non-existent');

        // When
        self::assertResponseStatusCodeSame(404);
    }
}
