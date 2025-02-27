<?php

declare(strict_types=1);

namespace App\Tests\Product\Infrastructure\API\Resource;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\CategoryFactory;
use App\Factory\ProductFactory;
use App\Factory\ProductVariantFactory;
use App\Factory\UserFactory;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

/**
 * @covers \App\Product\Infrastructure\Api\Resource\Product
 */
class ProductTest extends ApiTestCase
{
    use Factories;

    public const API_URL = '/api/products';

    public function testCreateProduct(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);

        $client->loginUser($user);

        // Step 1: Create a new product
        $response = $client->request('POST', '/api/products', [
            'json' => [
                'name' => 'Test Product',
                'description' => 'This is a test product description.',
            ],
        ]);

        // Step 2: Verify the response
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains([
            'name' => 'Test Product',
            'description' => 'This is a test product description.',
        ]);

        // Step 3: Verify the product was created in the database
        $responseData = $response->toArray();
        $productId = $responseData['id'];

        $productRepository = self::getContainer()->get(ProductRepository::class);
        $product = $productRepository->findOneById(Uuid::fromString($productId));

        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals('This is a test product description.', $product->getDescription());
    }

    public function testCreateProductWithVariants(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        $requestData = [
            'name' => 'Test Product',
            'description' => 'This is a test product description.',
            'variants' => [
                [
                    'name' => 'Variant 1',
                    'description' => 'First variant description',
                ],
                [
                    'name' => 'Variant 2',
                    'description' => 'Second variant description',
                ],
            ],
        ];

        // When
        $response = $client->request('POST', '/api/products', [
            'json' => $requestData,
        ]);

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains([
            'name' => 'Test Product',
            'description' => 'This is a test product description.',
        ]);

        $responseData = $response->toArray();
        $productId = $responseData['id'];

        $productRepository = self::getContainer()->get(ProductRepository::class);
        $product = $productRepository->findOneById(Uuid::fromString($productId));

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

    public function testGetSingleProduct(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        // Create a product to retrieve
        /** @var Product $product */
        $product = ProductFactory::createOne();

        // Step 1: Retrieve the product by ID
        $response = $client->request('GET', '/api/products/'.$product->getId()->toString());

        // Step 2: Verify the response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'id' => $product->getId()->toRfc4122(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
        ]);
    }

    public function testGetProductWithVariants(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        /** @var Product $product */
        $product = ProductFactory::createOne([
            'name' => 'Test Product',
            'description' => 'Test Description',
        ]);

        $variants = ProductVariantFactory::createMany(2, [
            'product' => $product,
        ]);

        // When
        $response = $client->request('GET', '/api/products/'.$product->getId()->toString());

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'id' => $product->getId()->toRfc4122(),
            'name' => 'Test Product',
            'description' => 'Test Description',
            'variants' => [
                [
                    'id' => $variants[0]->getId()->toRfc4122(),
                    'name' => $variants[0]->getName(),
                    'description' => $variants[0]->getDescription(),
                ],
                [
                    'id' => $variants[1]->getId()->toRfc4122(),
                    'name' => $variants[1]->getName(),
                    'description' => $variants[1]->getDescription(),
                ],
            ],
        ]);

        $responseData = $response->toArray();
        $this->assertCount(2, $responseData['variants']);
        $this->assertArrayHasKey('slug', $responseData['variants'][0]);
        $this->assertArrayHasKey('slug', $responseData['variants'][1]);
        $this->assertNotEmpty($responseData['variants'][0]['id']);
        $this->assertNotEmpty($responseData['variants'][1]['id']);
    }

    public function testDeleteProduct(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        // Given: Create a product to delete
        /** @var Product $product */
        $product = ProductFactory::createOne();

        // When: Send a DELETE request to remove the product
        $response = $client->request('DELETE', '/api/products/'.$product->getId()->toString());

        // Then: Verify the response and ensure the product is deleted
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $productRepository = self::getContainer()->get(ProductRepository::class);
        $deletedProduct = $productRepository->findOneById($product->getId());

        $this->assertNull($deletedProduct);
    }

    public function testShouldCreateProductWithCategories(): void
    {
        $client = static::createClient();
        // Given
        $category1 = CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $category2 = CategoryFactory::createOne([
            'name' => 'Laptops',
            'slug' => 'laptops',
        ]);

        $payload = [
            'name' => 'Test Product',
            'description' => 'This is a test product with categories',
            'categories' => [
                $category1->getId()->toString(),
                $category2->getId()->toString(),
            ],
        ];

        // When
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);

        $client->loginUser($user);

        $response = $client->request('POST', self::API_URL, ['json' => $payload]);
        $content = $response->toArray();

        // Then
        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            'name' => 'Test Product',
            'description' => 'This is a test product with categories',
            'categories' => [
                $category1->getId()->toString(),
                $category2->getId()->toString(),
            ],
        ]);
        self::assertTrue(Uuid::isValid($content['id']));
    }

    public function testShouldCreateProductWithVariantsAndCategories(): void
    {
        // Given
        $client = static::createClient();
        $category = CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $payload = [
            'name' => 'Smartphone',
            'description' => 'Latest smartphone model',
            'variants' => [
                [
                    'name' => '128GB Model',
                    'description' => 'Basic storage option',
                ],
                [
                    'name' => '256GB Model',
                    'description' => 'Extended storage option',
                ],
            ],
            'categories' => [
                $category->getId()->toString(),
            ],
        ];

        // When
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);

        $client->loginUser($user);

        $response = $client->request('POST', self::API_URL, ['json' => $payload]);
        $content = $response->toArray();

        // Then
        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            'name' => 'Smartphone',
            'description' => 'Latest smartphone model',
            'categories' => [
                $category->getId()->toString(),
            ],
        ]);

        self::assertCount(2, $content['variants']);
        self::assertEquals('128GB Model', $content['variants'][0]['name']);
        self::assertEquals('Basic storage option', $content['variants'][0]['description']);
        self::assertEquals('256GB Model', $content['variants'][1]['name']);
        self::assertEquals('Extended storage option', $content['variants'][1]['description']);
        self::assertTrue(Uuid::isValid($content['id']));
    }

    public function testShouldReturn400WhenCategoryDoesNotExist(): void
    {
        // Given
        $client = static::createClient();

        $payload = [
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'categories' => [
                Uuid::v4()->toString(),
            ],
        ];

        // When
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);

        $client->loginUser($user);

        $client->request('POST', self::API_URL, ['json' => $payload]);

        // Then
        self::assertResponseStatusCodeSame(400);
        self::assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
        self::assertJsonContains([
            'detail' => 'Category not found',
        ]);
    }
}
