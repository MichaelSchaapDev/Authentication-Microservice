<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AccessTokenExtractionException;
use App\Exceptions\AccessTokenIssueException;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;

class AccessTokenService
{
    private AccessTokenController $accessTokenController;
    private string $clientId;
    private string $clientSecret;

    /**
     * Create a new AccessTokenService instance.
     *
     * @param AccessTokenController $accessTokenController The access token controller used to issue new tokens
     * @param string $clientId The client ID of the OAuth client
     * @param string $clientSecret The client secret of the OAuth client
     */
    public function __construct(
        AccessTokenController $accessTokenController,
        string $clientId,
        string $clientSecret
    ) {
        $this->accessTokenController = $accessTokenController;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function fetchToken(array $credentials, string $scope): array
    {
        $psrRequest = $this->createPsrRequest($credentials, $scope);

        $tokenResponse = $this->issueToken($psrRequest);

        return $this->extractTokens($tokenResponse);
    }

    /**
     * Creates a new server request with the login credentials as the request body.
     */
    private function createPsrRequest(array $credentials, string $scope): ServerRequestInterface
    {
        $psrFactory = new Psr17Factory();

        $uri = env('SECONDARY_OAUTH_URL') . '/oauth/token';

        $body = [
            'grant_type' => 'password',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'username' => $credentials['email'],
            'password' => $credentials['password'],
            'scope' => $scope,
        ];

        return $psrFactory
            ->createServerRequest('POST', $uri)
            ->withParsedBody($body);
    }

    /**
     * Issues an access token using the AccessTokenController.
     *
     * @throws AccessTokenIssueException
     */
    private function issueToken(ServerRequestInterface $request)
    {
        try {
            return $this->accessTokenController->issueToken($request);
        } catch (\Exception $e) {
            throw new AccessTokenIssueException($e->getMessage());
        }
    }

    /**
     * Extracts the access token from the token response.
     *
     * @throws AccessTokenExtractionException
     */
    private function extractTokens($tokenResponse): array
    {
        $content = $tokenResponse->getContent();
        $decodedContent = json_decode($content, true);
        if (isset($decodedContent['access_token'])) {
            return $decodedContent;
        }

        throw new AccessTokenExtractionException('Could not extract access token from JSON response.');
    }
}
