<?php

declare(strict_types=1);

namespace App\Tests\Auth\Infrastructure\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Auth\Domain\Entity\User;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class UserResourceTest extends ApiTestCase
{
    use Factories;

    public function testPostUser(): void
    {
        $client = static::createClient();
        $response = $client->request('POST', '/api/users', [
            'json' => [
                'email' => 'test@example.com',
                'password' => 'plainpassword123',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJson($response->getContent());
        $this->assertJsonContains([
            'email' => 'test@example.com',
        ]);
    }

    public function testGetUser(): void
    {
        /** @var User $user */
        $user = UserFactory::createOne();

        $client = static::createClient();

        $client->loginUser($user);

        // Then, get the created user
        $response = $client->request('GET', '/api/users/'.$user->getId()->toString());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($response->getContent());
        $this->assertJsonContains([
            'id' => $user->getId()->toString(),
            'email' => $user->getEmail(),
        ]);
    }
}
