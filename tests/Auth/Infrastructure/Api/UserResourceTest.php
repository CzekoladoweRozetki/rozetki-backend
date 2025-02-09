<?php

declare(strict_types=1);

namespace App\Tests\Auth\Infrastructure\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserResourceTest extends ApiTestCase
{
    public function testPostUser(): void
    {
        $client = static::createClient();
        $response = $client->request('POST', '/api/users', [
            'json' => [
                'email' => 'test@example.com',
                'password' => 'plainpassword123'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJson($response->getContent());
        $this->assertJsonContains([
            'email' => 'test@example.com'
        ]);
    }
}
