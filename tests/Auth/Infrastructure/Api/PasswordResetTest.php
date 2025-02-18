<?php

declare(strict_types=1);

namespace App\Tests\Auth\Infrastructure\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Auth\Domain\Repository\PasswordResetTokenRepository;
use App\Auth\Domain\Repository\UserRepository;
use App\Factory\UserFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Test\Factories;

class PasswordResetTest extends ApiTestCase
{
    use Factories;

    public function testPasswordResetRequest(): void
    {
        // Given
        $client = static::createClient();
        $newPassword = 'newPassword123';
        /**
         * @var PasswordResetTokenRepository $passwordResetTokenRepository
         */
        $passwordResetTokenRepository = self::getContainer()->get(PasswordResetTokenRepository::class);
        /**
         * @var UserRepository $userRepository
         */
        $userRepository = self::getContainer()->get(UserRepository::class);
        /**
         * @var UserPasswordHasherInterface $passwordHasher
         */
        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);

        // Create a user and save it to the repository
        $user = UserFactory::createOne();

        // When
        $client->request('POST', '/api/password_reset_requests', [
            'json' => [
                'email' => $user->getEmail(),
            ],
        ]);

        $token = $passwordResetTokenRepository->findOneByUser($user);

        $client->request('POST', '/api/password_resets', [
            'json' => [
                'token' => $token->getId(),
                'newPassword' => $newPassword,
            ],
        ]);

        // Then
        $this->assertResponseStatusCodeSame(204);

        $updatedUser = $userRepository->findOneByEmail($user->getEmail());
        $this->assertNotNull($updatedUser);
        $this->assertTrue($passwordHasher->isPasswordValid($updatedUser, $newPassword));
    }
}
