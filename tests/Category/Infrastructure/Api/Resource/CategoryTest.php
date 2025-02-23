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
}
