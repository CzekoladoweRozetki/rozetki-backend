<?php

declare(strict_types=1);

namespace App\Tests\Product\Infrastructure\API\Resource;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

class ProductTest extends ApiTestCase
{
    use Factories;

    public function testCreateProduct(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne();

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

        $productRepository = self::getContainer()->get('App\Product\Domain\Repository\ProductRepository');
        $product = $productRepository->findById(Uuid::fromString($productId));

        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals('This is a test product description.', $product->getDescription());
    }

    public function testGetSingleProduct(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne();
        $client->loginUser($user);

        // Create a product to retrieve
        $product = ProductFactory::createOne();

        // Step 1: Retrieve the product by ID
        $response = $client->request('GET', '/api/products/' . $product->getId()->toString());

        // Step 2: Verify the response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'id' => $product->getId()->toRfc4122(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
        ]);
    }
}
