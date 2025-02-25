<?php

declare(strict_types=1);

namespace App\Tests\Category\Infrastructure\Api\Resource;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Auth\Domain\UserRole;
use App\Factory\CategoryFactory;
use App\Factory\UserFactory;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

class CategoryTest extends ApiTestCase
{
    use Factories;

    private const API_URL = '/api/categories';

    private Client $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $user = UserFactory::createOne(['roles' => [UserRole::ROLE_ADMIN->value]]);
        $this->client->loginUser($user);
        parent::setUp();
    }

    public function testShouldCreateCategory(): void
    {
        // Given
        $payload = [
            'name' => 'Electronics',
            'slug' => 'electronics',
        ];

        // When
        $response = $this->client->request('POST', self::API_URL, ['json' => $payload]);
        $content = $response->toArray();

        // Then
        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);
        self::assertTrue(Uuid::isValid($content['id']));
    }

    public function testShouldCreateCategoryWithParent(): void
    {
        // Given
        $parent = CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $payload = [
            'name' => 'Laptops',
            'slug' => 'laptops',
            'parent' => $parent->getId()->toString(),
        ];

        // When
        $response = $this->client->request('POST', self::API_URL, ['json' => $payload]);
        $content = $response->toArray();

        // Then
        self::assertResponseStatusCodeSame(201);
        self::assertJsonContains([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'parent' => $parent->getId()->__toString(),
        ]);
    }

    public function testShouldGenerateSlugWhenNotProvided(): void
    {
        // Given
        $payload = [
            'name' => 'Gaming Laptops',
        ];

        // When
        $response = $this->client->request('POST', self::API_URL, ['json' => $payload]);

        // Then
        self::assertResponseStatusCodeSame(201);
        self::assertJsonContains([
            'name' => 'Gaming Laptops',
            'slug' => 'gaming-laptops',
        ]);
    }

    public function testShouldReturn400WhenNameIsEmpty(): void
    {
        // Given
        $payload = [
            'name' => '',
            'slug' => 'electronics',
        ];

        // When
        $response = $this->client->request('POST', self::API_URL, ['json' => $payload]);

        // Then
        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
        self::assertJsonContains([
            'detail' => 'name: Name should not be blank',
        ]);
    }

    public function testShouldReturn400WhenParentDoesNotExist(): void
    {
        // Given
        $payload = [
            'name' => 'Laptops',
            'slug' => 'laptops',
            'parent' => Uuid::v4()->__toString(),
        ];

        // When
        $this->client->request('POST', self::API_URL, ['json' => $payload]);

        // Then
        self::assertResponseStatusCodeSame(400);
        self::assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
        self::assertJsonContains([
            'detail' => 'Category not found',
        ]);
    }

    public function testShouldGetCategory(): void
    {
        // Given
        $category = CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        // When
        $response = $this->client->request('GET', self::API_URL.'/'.$category->getId());

        // Then
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            'id' => $category->getId()->__toString(),
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);
    }

    public function testShouldReturn404WhenCategoryNotFound(): void
    {
        // Given
        $nonExistentId = Uuid::v4();

        // When
        $this->client->request('GET', self::API_URL.'/'.$nonExistentId);

        // Then
        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
    }

    public function testShouldGetCategoryCollection(): void
    {
        // Given
        $electronics = CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        CategoryFactory::createOne([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'parent' => $electronics,
        ]);

        CategoryFactory::createOne([
            'name' => 'Books',
            'slug' => 'books',
        ]);

        // When
        $response = $this->client->request('GET', self::API_URL);
        $content = $response->toArray();

        // Then
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertCount(3, $content['member']);
        self::assertJsonContains([
            'totalItems' => 3,
            'member' => [
                [
                    'name' => 'Electronics',
                    'slug' => 'electronics',
                ],
                [
                    'name' => 'Laptops',
                    'slug' => 'laptops',
                    'parent' => $electronics->getId()->__toString(),
                ],
                [
                    'name' => 'Books',
                    'slug' => 'books',
                ],
            ],
        ]);
    }

    public function testShouldGetEmptyCategoryCollection(): void
    {
        // When
        $response = $this->client->request('GET', self::API_URL);
        $content = $response->toArray();

        // Then
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertCount(0, $content['member']);
        self::assertJsonContains([
            'totalItems' => 0,
            'member' => [],
        ]);
    }

    public function testShouldUpdateCategory(): void
    {
        // Given
        $category = CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $payload = [
            'id' => $category->getId()->toString(),
            'name' => 'Updated Electronics',
            'slug' => 'updated-electronics',
        ];

        // When
        $response = $this->client->request(
            'PUT',
            self::API_URL.'/'.$category->getId()->toString(),
            ['json' => $payload]
        );

        // Then
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'id' => $category->getId()->toString(),
            'name' => 'Updated Electronics',
            'slug' => 'updated-electronics',
        ]);
    }

    public function testShouldUpdateCategoryWithParent(): void
    {
        // Given
        $parent = CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $category = CategoryFactory::createOne([
            'name' => 'Laptops',
            'slug' => 'laptops',
        ]);

        $payload = [
            'id' => $category->getId()->toString(),
            'name' => 'Gaming Laptops',
            'slug' => 'gaming-laptops',
            'parent' => $parent->getId()->__toString(),
        ];

        // When
        $response = $this->client->request('PUT', self::API_URL.'/'.$category->getId(), ['json' => $payload]);

        // Then
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'name' => 'Gaming Laptops',
            'slug' => 'gaming-laptops',
            'parent' => $parent->getId()->__toString(),
        ]);
    }

    public function testShouldReturn422WhenUpdatingWithEmptyName(): void
    {
        // Given
        $category = CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $payload = [
            'id' => $category->getId()->toString(),
            'name' => '',
            'slug' => 'electronics',
        ];

        // When
        $this->client->request('PUT', self::API_URL.'/'.$category->getId(), ['json' => $payload]);

        // Then
        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
        self::assertJsonContains([
            'detail' => 'name: Name should not be blank',
        ]);
    }

    public function testShouldReturn400WhenUpdatingWithNonExistentParent(): void
    {
        // Given
        $category = CategoryFactory::createOne([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $payload = [
            'id' => $category->getId()->toString(),
            'name' => 'Updated Electronics',
            'slug' => 'updated-electronics',
            'parent' => Uuid::v4()->__toString(),
        ];

        // When
        $this->client->request('PUT', self::API_URL.'/'.$category->getId(), ['json' => $payload]);

        // Then
        self::assertResponseStatusCodeSame(400);
        self::assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
        self::assertJsonContains([
            'detail' => 'Category not found',
        ]);
    }
}
