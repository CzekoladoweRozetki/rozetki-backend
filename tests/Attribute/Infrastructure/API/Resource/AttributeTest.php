<?php

declare(strict_types=1);

namespace App\Tests\Attribute\Infrastructure\API\Resource;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Attribute\Domain\Entity\Attribute as AttributeEntity;
use App\Attribute\Domain\Repository\AttributeRepository;
use App\Factory\AttributeFactory;
use App\Factory\AttributeValueFactory;
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
        ]);

        $responseData = $response->toArray();
        $this->assertEquals('Color', $responseData['name']);
        $this->assertCount(3, $responseData['values']);

        // Check if values contain the expected strings
        $valueTexts = array_map(
            fn ($value) => $value['value'],
            $responseData['values']
        );
        $this->assertContains('Red', $valueTexts);
        $this->assertContains('Green', $valueTexts);
        $this->assertContains('Blue', $valueTexts);

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
            'parentId' => $parentAttribute->getId()->toString(),
        ]);

        $responseData = $response->toArray();
        $this->assertEquals('Size', $responseData['name']);
        $this->assertEquals($parentAttribute->getId()->toString(), $responseData['parentId']);
        $this->assertCount(3, $responseData['values']);

        // Check if values contain the expected strings
        $valueTexts = array_map(
            fn ($value) => $value['value'],
            $responseData['values']
        );
        $this->assertContains('Small', $valueTexts);
        $this->assertContains('Medium', $valueTexts);
        $this->assertContains('Large', $valueTexts);

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

    public function testGetAttribute(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        /** @var AttributeEntity $attribute */
        $attribute = AttributeFactory::createOne([
            'name' => 'Material',
        ]);

        // Add values to the attribute
        AttributeValueFactory::createOne([
            'value' => 'Cotton',
            'attribute' => $attribute,
        ]);
        AttributeValueFactory::createOne([
            'value' => 'Polyester',
            'attribute' => $attribute,
        ]);
        AttributeValueFactory::createOne([
            'value' => 'Wool',
            'attribute' => $attribute,
        ]);

        // When
        $response = $client->request('GET', self::API_URL.'/'.$attribute->getId()->toString());

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'id' => $attribute->getId()->toString(),
            'name' => 'Material',
        ]);

        $responseData = $response->toArray();
        $this->assertCount(3, $responseData['values']);

        // Verify that each value has the expected structure
        foreach ($responseData['values'] as $value) {
            $this->assertArrayHasKey('id', $value);
            $this->assertArrayHasKey('value', $value);
            $this->assertArrayHasKey('attributeId', $value);
            $this->assertEquals($attribute->getId()->toString(), $value['attributeId']);
        }
    }

    public function testGetAttributeWithParent(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        /** @var AttributeEntity $parentAttribute */
        $parentAttribute = AttributeFactory::createOne([
            'name' => 'Product Specifications',
        ]);

        /** @var AttributeEntity $attribute */
        $attribute = AttributeFactory::createOne([
            'name' => 'Weight',
            'parent' => $parentAttribute,
        ]);

        // Add values to the attribute
        AttributeValueFactory::createOne([
            'value' => 'Light',
            'attribute' => $attribute,
        ]);

        // When
        $response = $client->request('GET', self::API_URL.'/'.$attribute->getId()->toString());

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'id' => $attribute->getId()->toString(),
            'name' => 'Weight',
            'parentId' => $parentAttribute->getId()->toString(),
        ]);

        $responseData = $response->toArray();
        $this->assertCount(1, $responseData['values']);
        $this->assertEquals('Light', $responseData['values'][0]['value']);
    }

    public function testGetAttributeWithoutAdminRole(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);
        $client->loginUser($user);

        /** @var AttributeEntity $attribute */
        $attribute = AttributeFactory::createOne([
            'name' => 'Color',
        ]);

        // When
        $client->request('GET', self::API_URL.'/'.$attribute->getId()->toString());

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testGetNonExistentAttribute(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        $nonExistentId = Uuid::v4()->toString();

        // When
        $client->request('GET', self::API_URL.'/'.$nonExistentId);

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteAttribute(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        /** @var AttributeEntity $attribute */
        $attribute = AttributeFactory::createOne([
            'name' => 'Texture',
        ]);

        $attributeId = $attribute->getId();

        // Add values to the attribute
        AttributeValueFactory::createMany(2, [
            'attribute' => $attribute,
        ]);

        // When
        $client->request('DELETE', self::API_URL.'/'.$attributeId->toString());

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $attributeRepository = self::getContainer()->get(AttributeRepository::class);
        $deletedAttribute = $attributeRepository->findOneById($attributeId);
        $this->assertNull($deletedAttribute);
    }

    public function testDeleteAttributeWithChildren(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        /** @var AttributeEntity $parentAttribute */
        $parentAttribute = AttributeFactory::createOne([
            'name' => 'Parent Feature',
        ]);

        /** @var AttributeEntity $childAttribute */
        $childAttribute = AttributeFactory::createOne([
            'name' => 'Child Feature',
            'parent' => $parentAttribute,
        ]);

        $parentId = $parentAttribute->getId();
        $childId = $childAttribute->getId();

        // When
        $client->request('DELETE', self::API_URL.'/'.$parentId->toString());

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $attributeRepository = self::getContainer()->get(AttributeRepository::class);

        // Parent should be removed
        $deletedParent = $attributeRepository->findOneById($parentId);
        $this->assertNull($deletedParent);

        $child = $attributeRepository->findOneById($childId);
        $this->assertNull($child);
    }

    public function testDeleteAttributeWithoutAdminRole(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);
        $client->loginUser($user);

        /** @var AttributeEntity $attribute */
        $attribute = AttributeFactory::createOne([
            'name' => 'Feature',
        ]);

        // When
        $client->request('DELETE', self::API_URL.'/'.$attribute->getId()->toString());

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        // Verify attribute still exists
        $attributeRepository = self::getContainer()->get(AttributeRepository::class);
        $this->assertNotNull($attributeRepository->findOneById($attribute->getId()));
    }

    public function testDeleteNonExistentAttribute(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        $nonExistentId = Uuid::v4()->toString();

        // When
        $client->request('DELETE', self::API_URL.'/'.$nonExistentId);

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteAttributeCascadesValues(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        /** @var AttributeEntity $attribute */
        $attribute = AttributeFactory::createOne([
            'name' => 'Material',
        ]);

        // Add values to the attribute
        $values = AttributeValueFactory::createMany(3, [
            'attribute' => $attribute,
        ]);

        $valueIds = array_map(
            fn ($value) => $value->getId()->toString(),
            $values
        );

        // When
        $client->request('DELETE', self::API_URL.'/'.$attribute->getId()->toString());

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        // Check that values are also deleted
        foreach ($valueIds as $valueId) {
            $value = $entityManager->getRepository('App\Attribute\Domain\Entity\AttributeValue')
                ->find(Uuid::fromString($valueId));
            $this->assertNull($value, 'Value was not deleted with its attribute');
        }
    }

    public function testGetAttributesCollection(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        // Create test attributes with values
        /** @var AttributeEntity $attribute1 */
        $attribute1 = AttributeFactory::createOne([
            'name' => 'Color',
        ]);

        $values1 = AttributeValueFactory::createMany(3, [
            'attribute' => $attribute1,
        ]);

        /** @var AttributeEntity $attribute2 */
        $attribute2 = AttributeFactory::createOne([
            'name' => 'Size',
        ]);

        $values2 = AttributeValueFactory::createMany(2, [
            'attribute' => $attribute2,
        ]);

        // When
        $response = $client->request('GET', self::API_URL);

        // Then
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = $response->toArray();
        $this->assertCount(2, $responseData['member']);

        // Verify both attributes are in the response
        $foundColor = false;
        $foundSize = false;

        foreach ($responseData['member'] as $attribute) {
            if ('Color' === $attribute['name']) {
                $foundColor = true;
                $this->assertEquals($attribute1->getId()->toString(), $attribute['id']);
                $this->assertCount(3, $attribute['values']);

                // Check color values
                $values = array_map(fn ($v) => $v['value'], $attribute['values']);
                foreach ($values1 as $value) {
                    $this->assertContains($value->getValue(), $values);
                }
            }

            if ('Size' === $attribute['name']) {
                $foundSize = true;
                $this->assertEquals($attribute2->getId()->toString(), $attribute['id']);
                $this->assertCount(2, $attribute['values']);

                // Check size values
                $values = array_map(fn ($v) => $v['value'], $attribute['values']);
                foreach ($values2 as $value) {
                    $this->assertContains($value->getValue(), $values);
                }
            }
        }

        $this->assertTrue($foundColor, 'Color attribute not found in response');
        $this->assertTrue($foundSize, 'Size attribute not found in response');
    }

    public function testGetAttributesCollectionWithParentChild(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        // Create parent attribute
        /** @var AttributeEntity $parentAttribute */
        $parentAttribute = AttributeFactory::createOne([
            'name' => 'Product Features',
        ]);

        // Create child attribute
        /** @var AttributeEntity $childAttribute */
        $childAttribute = AttributeFactory::createOne([
            'name' => 'Material',
            'parent' => $parentAttribute,
        ]);

        $childAttributeValues = AttributeValueFactory::createMany(2, [
            'attribute' => $childAttribute,
        ]);

        // When
        $response = $client->request('GET', self::API_URL);

        // Then
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = $response->toArray();
        $this->assertGreaterThanOrEqual(2, count($responseData));

        // Find the parent attribute
        $foundParent = null;
        $foundChild = null;

        foreach ($responseData['member'] as $attribute) {
            if ('Product Features' === $attribute['name']) {
                $foundParent = $attribute;
            }
            if ('Material' === $attribute['name']) {
                $foundChild = $attribute;
            }
        }

        $this->assertNotNull($foundParent, 'Parent attribute not found in response');
        $this->assertEquals($parentAttribute->getId()->toString(), $foundParent['id']);

        $this->assertNotNull($foundChild, 'Child attribute not found in response');
        $this->assertEquals($childAttribute->getId()->toString(), $foundChild['id']);
        $this->assertEquals($parentAttribute->getId()->toString(), $foundChild['parentId']);
        $this->assertCount(2, $foundChild['values']);

        // Check child values
        $values = array_map(fn ($v) => $v['value'], $foundChild['values']);
        foreach ($childAttributeValues as $childAttributeValue) {
            $this->assertContains($childAttributeValue->getValue(), $values);
        }
    }

    public function testGetAttributesCollectionWithoutAdminRole(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);
        $client->loginUser($user);

        // Create an attribute to ensure there's data in the database
        AttributeFactory::createOne([
            'name' => 'Feature',
        ]);

        // When
        $client->request('GET', self::API_URL);

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testGetAttributesCollectionWithNoAttributes(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        // Clear any attributes created in previous tests
        AttributeFactory::repository()->truncate();

        // When
        $response = $client->request('GET', self::API_URL);

        // Then
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = $response->toArray();
        $this->assertEmpty($responseData['member']);
    }
}
