<?php

declare(strict_types=1);

namespace App\Tests\Auth;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\UserFactory;
use League\Bundle\OAuth2ServerBundle\Manager\ClientManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\Client;
use League\Bundle\OAuth2ServerBundle\ValueObject\RedirectUri;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class OAuthLoginTest extends ApiTestCase
{
    use Factories;

    public function testAuthorizationCodeFlow(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        /**
         * @var ClientManagerInterface $clientManager
         */
        $clientManager = self::getContainer()->get(ClientManagerInterface::class);
        $oauthClient = new Client('test', 'test', null);
        $oauthClient->setRedirectUris(new RedirectUri('https://example.com/callback'));
        $clientManager->save($oauthClient);

        $user = UserFactory::createOne([
            'email' => 'test@example.com',
            'password' => 'plainpassword123',
        ]);

        $client->loginUser($user);

        $codeVerifier = bin2hex(random_bytes(64));
        $codeChallengeMethod = 'S256';

        $codeChallenge = strtr(
            rtrim(base64_encode(hash('sha256', $codeVerifier, true)), '='),
            '+/',
            '-_'
        );

        // Step 1: Simulate user login and obtain authorization code
        $response = $client->request('GET', '/authorize', [
            'query' => [
                'response_type' => 'code',
                'client_id' => 'test',
                'scope' => 'email',
                'state' => 'test_state',
                'redirect_uri' => 'https://example.com/callback',
                'code_challenge' => $codeChallenge,
                'code_challenge_method' => $codeChallengeMethod,
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $location = $response->getHeaders(false)['location'][0];
        $this->assertStringContainsString('https://example.com/callback', $location);

        // Extract authorization code from the redirect URL
        parse_str(parse_url($location, PHP_URL_QUERY), $queryParams);
        $authorizationCode = $queryParams['code'];

        // Step 2: Exchange authorization code for access token
        $response = $client->request('POST', '/token', [
            'json' => [
                'grant_type' => 'authorization_code',
                'client_id' => 'test',
                'client_secret' => 'test',
                'redirect_uri' => 'https://example.com/callback',
                'code' => $authorizationCode,
                'code_verifier' => $codeVerifier,
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseData = $response->toArray();
        $this->assertArrayHasKey('access_token', $responseData);
        $this->assertArrayHasKey('refresh_token', $responseData);
        $this->assertArrayHasKey('expires_in', $responseData);

        // Step 3: Use access token to access a protected resource
        $accessToken = $responseData['access_token'];
        $response = $client->request('GET', '/api/protected_tests', [
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($response->getContent());
    }
}
