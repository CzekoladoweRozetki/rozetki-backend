<?php

declare(strict_types=1);

namespace App\Tests\Test\Infrastructure\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\TestFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class TestResourceTest extends ApiTestCase
{
    use Factories;

    public function testGetCollection(): void
    {
        $client = static::createClient();

        $tests = TestFactory::createMany(10);

        $response = $client->request('GET', '/api/tests');

        //dd($response->getContent());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'totalItems' => 10,
        ]);
    }

    public function testGet(): void
    {
        $client = static::createClient();

        $test = TestFactory::createOne();

        $client->request('GET', '/api/tests/' . $test->getId()->toString());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'id' => $test->getId()->toString(),
            'name' => $test->getName(),
        ]);
    }

    public function testPost(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/tests', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'name' => 'Test Name',
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testPatch(): void
    {
        $test = TestFactory::createOne();
        $client = static::createClient();
        $client->request('PATCH', '/api/tests/' . $test->getId()->toString(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'name' => 'New Name',
            ]
        ]);

        $updatedTest = TestFactory::repository()->find($test->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals('New Name', $updatedTest->getName());
    }

    public function testDelete(): void
    {
        $test = TestFactory::createOne();

        $client = static::createClient();
        $url = '/api/tests/' . $test->getId()->toString();
        $client->request('DELETE', '/api/tests/' . $test->getId()->toString());

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $this->assertNull(TestFactory::repository()->find($test->getId()));
    }
}
