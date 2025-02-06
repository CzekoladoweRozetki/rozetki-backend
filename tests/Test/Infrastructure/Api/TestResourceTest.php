<?php

declare(strict_types=1);

namespace App\Tests\Test\Infrastructure\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\TestFactory;
use Symfony\Component\HttpFoundation\Response;

class TestResourceTest extends ApiTestCase
{
//    public function testGetCollection(): void
//    {
//        $client = static::createClient();
//        $client->request('GET', '/tests');
//
//        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
//        $this->assertJson($client->getResponse()->getContent());
//    }

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

//    public function testPost(): void
//    {
//        $client = static::createClient();
//        $client->request('POST', '/tests', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
//            'id' => '1',
//            'name' => 'Test Name',
//        ]));
//
//        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
//        $this->assertJson($client->getResponse()->getContent());
//    }
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
