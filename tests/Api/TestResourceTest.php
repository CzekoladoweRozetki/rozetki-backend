<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\TestFactory;
use Symfony\Component\HttpFoundation\Response;

class TestResourceTest extends ApiTestCase
{
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
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'name' => 'Test Name',
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJson($client->getResponse()->getContent());
    }
//
//    public function testPatch(): void
//    {
//        $client = static::createClient();
//        $client->request('PATCH', '/tests/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
//            'name' => 'Updated Test Name',
//        ]));
//
//        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
//        $this->assertJson($client->getResponse()->getContent());
//    }
//
//    public function testDelete(): void
//    {
//        $client = static::createClient();
//        $client->request('DELETE', '/tests/1');
//
//        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
//    }
}
