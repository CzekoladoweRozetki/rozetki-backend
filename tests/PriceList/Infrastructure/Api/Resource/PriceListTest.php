<?php

declare(strict_types=1);

namespace App\Tests\PriceList\Infrastructure\Api\Resource;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\PriceListFactory;
use App\Factory\UserFactory;
use App\PriceList\Domain\Repository\PriceListRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

/**
 * @covers \App\PriceList\Infrastructure\Api\Resource\PriceList
 */
class PriceListTest extends ApiTestCase
{
    use Factories;

    public const API_URL = '/api/price_lists';

    public function testCreatePriceList(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        // Step 1: Create a new price list
        $response = $client->request('POST', self::API_URL, [
            'json' => [
                'name' => 'Standard Pricing',
                'currency' => 'USD',
            ],
        ]);

        // Step 2: Verify the response
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains([
            'name' => 'Standard Pricing',
            'currency' => 'USD',
        ]);

        // Step 3: Verify the price list was created in the database
        $responseData = $response->toArray();
        $priceListId = $responseData['id'];

        $this->assertTrue(Uuid::isValid($priceListId));

        $priceListRepository = self::getContainer()->get(PriceListRepository::class);
        $priceList = $priceListRepository->findOneById(Uuid::fromString($priceListId));

        $this->assertNotNull($priceList);
        $this->assertEquals('Standard Pricing', $priceList->getName());
        $this->assertEquals('USD', $priceList->getCurrency());
    }

    public function testCreatePriceListRequiresAdminRole(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);
        $client->loginUser($user);

        // Try to create a price list with non-admin user
        $client->request('POST', self::API_URL, [
            'json' => [
                'name' => 'Standard Pricing',
                'currency' => 'USD',
            ],
        ]);

        // Verify access is denied
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreatePriceListWithInvalidCurrency(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        // Try to create a price list with invalid currency
        $client->request('POST', self::API_URL, [
            'json' => [
                'name' => 'Standard Pricing',
                'currency' => 'INVALID',
            ],
        ]);

        // Verify validation error
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonContains([
            'detail' => 'This value is not a valid currency.',
        ]);
    }

    public function testGetSinglePriceList(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        // First create a price list using the factory
        $priceList = PriceListFactory::createOne([
            'name' => 'Premium Pricing',
            'currency' => 'EUR',
        ]);
        $priceListId = $priceList->getId();

        // Then get the price list by ID
        $response = $client->request('GET', self::API_URL.'/'.$priceListId);

        // Verify the response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'id' => $priceListId->toString(),
            'name' => 'Premium Pricing',
            'currency' => 'EUR',
        ]);
    }

    public function testDeletePriceList(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        $pricelist = PriceListFactory::createOne();

        // Delete the price list
        $client->request('DELETE', self::API_URL.'/'.$pricelist->getId()->toString());

        // Verify deletion
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        // Verify it's gone from the database
        $priceListRepository = self::getContainer()->get(PriceListRepository::class);
        $deletedPriceList = $priceListRepository->findOneById($pricelist->getId());

        $this->assertNull($deletedPriceList);
    }

    public function testDeletePriceListRequiresAdminRole(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);
        $client->loginUser($user);

        // First create a price list using the factory
        $priceList = PriceListFactory::createOne([
            'name' => 'Premium Pricing',
            'currency' => 'EUR',
        ]);
        $priceListId = $priceList->getId();

        // Try to delete the price list with non-admin user
        $client->request('DELETE', self::API_URL.'/'.$priceListId);

        // Verify access is denied
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        // Verify the price list still exists
        $priceListRepository = self::getContainer()->get(PriceListRepository::class);
        $existingPriceList = $priceListRepository->findOneById($priceListId);

        $this->assertNotNull($existingPriceList);
    }

    public function testUpdatePriceList(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        $priceList = PriceListFactory::createOne([
            'name' => 'Initial Name',
            'currency' => 'USD',
        ]);
        $priceListId = $priceList->getId()->toString();

        $newName = 'Updated Name';
        $newCurrency = 'EUR';

        $response = $client->request('PUT', self::API_URL.'/'.$priceListId, [
            'json' => [
                'id' => $priceListId,
                'name' => $newName,
                'currency' => $newCurrency,
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'id' => $priceListId,
            'name' => $newName,
            'currency' => $newCurrency,
        ]);

        // Verify the changes in the database
        $priceListRepository = self::getContainer()->get(PriceListRepository::class);
        $updatedPriceList = $priceListRepository->findOneById(Uuid::fromString($priceListId));

        $this->assertNotNull($updatedPriceList);
        $this->assertEquals($newName, $updatedPriceList->getName());
        $this->assertEquals($newCurrency, $updatedPriceList->getCurrency());
    }

    public function testUpdatePriceListRequiresAdminRole(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);
        $client->loginUser($user);

        $priceList = PriceListFactory::createOne([
            'name' => 'Initial Name',
            'currency' => 'USD',
        ]);
        $priceListId = $priceList->getId()->toString();

        $client->request('PUT', self::API_URL.'/'.$priceListId, [
            'json' => [
                'name' => 'Attempted Update Name',
                'currency' => 'EUR',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        // Verify the price list was not changed
        $priceListRepository = self::getContainer()->get(PriceListRepository::class);
        $existingPriceList = $priceListRepository->findOneById(Uuid::fromString($priceListId));

        $this->assertNotNull($existingPriceList);
        $this->assertEquals('Initial Name', $existingPriceList->getName());
        $this->assertEquals('USD', $existingPriceList->getCurrency());
    }

    public function testUpdatePriceListWithInvalidCurrency(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        $priceList = PriceListFactory::createOne([
            'name' => 'Initial Name',
            'currency' => 'USD',
        ]);
        $priceListId = $priceList->getId()->toString();

        $client->request('PUT', self::API_URL.'/'.$priceListId, [
            'json' => [
                'id' => $priceListId,
                'name' => 'Updated Name',
                'currency' => 'INVALID',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonContains([
            'detail' => 'This value is not a valid currency.',
        ]);
    }

    public function testUpdateNonExistentPriceList(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $client->loginUser($user);

        $nonExistentId = Uuid::v4()->toString();

        $client->request('PUT', self::API_URL.'/'.$nonExistentId, [
            'json' => [
                'id' => $nonExistentId,
                'name' => 'Updated Name',
                'currency' => 'EUR',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
