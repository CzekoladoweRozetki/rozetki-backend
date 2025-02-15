<?php

declare(strict_types=1);

namespace App\Tests\Product\Infrastructure\API\Resource;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Auth\Domain\Entity\ActivationToken;
use App\Auth\Domain\Repository\ActivationTokenRepository;
use App\Auth\Domain\Repository\UserRepository;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;

class UserActivationTest extends ApiTestCase
{
    use Factories;

    public function testUserActivation(): void
    {
        $client = static::createClient();

        // Given: Create a user and an activation token
        $user = UserFactory::createOne(['email' => 'test@example.com', 'password' => 'plainpassword']);
        $activationTokenRepository = self::getContainer()->get(ActivationTokenRepository::class);
        $token = ActivationToken::create($user->getEmail());
        $activationTokenRepository->save($token);

        // When: Send a POST request to activate the user
        $response = $client->request('POST', '/api/user_activations', [
            'json' => ['token' => $token->getToken()],
        ]);

        // Then: Verify the response and ensure the user is activated
        $this->assertResponseStatusCodeSame(204);

        $userRepository = self::getContainer()->get(UserRepository::class);
        $activatedUser = $userRepository->findOneByEmail('test@example.com');
        $this->assertTrue($activatedUser->isActive(), 'User was not activated.');
    }
}
