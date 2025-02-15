<?php

namespace App\Tests\Auth\Infrastructure\Controller;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiLoginControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);

        // Create a user with known credentials
        $user = UserFactory::createOne([
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
    }

    public function testSuccessfulLogin(): void
    {
        $this->client->request(
            'POST',
            '/login',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'username' => 'user@example.com',
                'password' => 'password',
            ])
        );

        $this->assertResponseIsSuccessful();
    }

    public function testUnsuccessfulLogin(): void
    {
        $this->client->request(
            'POST',
            '/login',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'username' => 'invalid@example.com',
                'password' => 'invalid',
            ])
        );

        $this->assertResponseStatusCodeSame(401);
    }
}
