<?php

declare(strict_types=1);

namespace App\Tests\Auth\Infrastructure\Api;

use App\Auth\Domain\Repository\PasswordResetTokenRepository;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

/**
 * @covers \App\Auth\Infrastructure\Api\Resource\PasswordResetRequest
 * @covers \App\Auth\Infrastructure\Api\Processor\PasswordResetRequestProcessor
 */
class PasswordResetRequestTest extends WebTestCase
{
    use Factories;

    public function testPasswordResetRequest(): void
    {
        // Given
        $client = static::createClient();
        $user = UserFactory::createOne();
        /**
         * @var PasswordResetTokenRepository $passwordResetTokenRepository
         */
        $passwordResetTokenRepository = static::getContainer()->get(PasswordResetTokenRepository::class);

        // When
        $client->request('POST', '/api/password_reset_requests', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['email' => $user->getEmail()]));

        // Then
        $this->assertResponseStatusCodeSame(204);

        $token = $passwordResetTokenRepository->findOneByUser($user);

        $this->assertNotNull($token);
        $this->assertSame($user->getId(), $token->getUser()->getId());
    }
}
