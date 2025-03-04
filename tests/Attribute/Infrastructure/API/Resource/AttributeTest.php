<?php

declare(strict_types=1);

namespace App\Tests\Attribute\Infrastructure\API\Resource;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Attribute\Domain\Entity\Attribute as AttributeEntity;
use App\Attribute\Domain\Repository\AttributeRepository;
use App\Factory\AttributeFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

/**
 * @covers \App\Attribute\Infrastructure\API\Resource\Attribute
 */
class AttributeTest extends ApiTestCase
{
    use Factories;

    public const API_URL = '/api/attributes';

    public function testCreateAttribute(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        $requestData = [
            'name' => 'Color',
            'values' => ['Red', 'Green', 'Blue'],
        ];

        // When
        $response = $client->request('POST', self::API_URL, [
            'json' => $requestData,
        ]);

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains([
            'name' => 'Color',
            'values' => ['Red', 'Green', 'Blue'],
        ]);

        $responseData = $response->toArray();
        $attributeId = $responseData['id'];

        $attributeRepository = self::getContainer()->get(AttributeRepository::class);
        $attribute = $attributeRepository->findOneById(Uuid::fromString($attributeId));

        $this->assertNotNull($attribute);
        $this->assertEquals('Color', $attribute->getName());
        $this->assertCount(3, $attribute->getValues());
        $this->assertNull($attribute->getParent());
    }

    public function testCreateAttributeWithParent(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        /** @var AttributeEntity $parentAttribute */
        $parentAttribute = AttributeFactory::createOne([
            'name' => 'Product Features',
        ]);

        $requestData = [
            'name' => 'Size',
            'values' => ['Small', 'Medium', 'Large'],
            'parentId' => $parentAttribute->getId()->toString(),
        ];

        // When
        $response = $client->request('POST', self::API_URL, [
            'json' => $requestData,
        ]);

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains([
            'name' => 'Size',
            'values' => ['Small', 'Medium', 'Large'],
            'parentId' => $parentAttribute->getId()->toString(),
        ]);

        $responseData = $response->toArray();
        $attributeId = $responseData['id'];

        $attributeRepository = self::getContainer()->get(AttributeRepository::class);
        $attribute = $attributeRepository->findOneById(Uuid::fromString($attributeId));

        $this->assertNotNull($attribute);
        $this->assertEquals('Size', $attribute->getName());
        $this->assertCount(3, $attribute->getValues());
        $this->assertNotNull($attribute->getParent());
        $this->assertEquals($parentAttribute->getId()->toString(), $attribute->getParent()->getId()->toString());
    }

    public function testCreateAttributeWithoutAdminRole(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);
        $client->loginUser($user);

        $requestData = [
            'name' => 'Weight',
            'values' => ['Light', 'Heavy'],
        ];

        // When
        $client->request('POST', self::API_URL, [
            'json' => $requestData,
        ]);

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreateAttributeWithEmptyName(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        $requestData = [
            'name' => '',
            'values' => ['Value1', 'Value2'],
        ];

        // When
        $client->request('POST', self::API_URL, [
            'json' => $requestData,
        ]);

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
    }

    public function testCreateAttributeWithInvalidParentId(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        $requestData = [
            'name' => 'Material',
            'values' => ['Cotton', 'Polyester', 'Wool'],
            'parentId' => Uuid::v4()->toString(),
        ];

        // When
        $client->request('POST', self::API_URL, [
            'json' => $requestData,
        ]);

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
        $this->assertJsonContains([
            'detail' => 'Parent attribute not found',
        ]);
    }
}
