<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomAuthenticationException;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;

class AuthController extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * Log the user out of the application and revoke their token.
     *
     * @throws CustomAuthenticationException
     */
    public function logout(): JsonResponse
    {
        $accessTokenId = $this->getAccessTokenIdFromAccessToken();

        // Revoke the access token and the corresponding refresh token
        RefreshToken::where('access_token_id', $accessTokenId)->delete();
        Token::where('id', $accessTokenId)->delete();

        return response()->json([
            'data' => [
                'type' => 'messages',
                'attributes' => [
                    'message' => 'You have been successfully logged out.',
                ],
            ],
            'meta' => [
                'http_code' => (string) JsonResponse::HTTP_OK,
            ],
        ]);
    }

    /**
     * Get the access token ID through the access token in the current request.
     *
     * @throws CustomAuthenticationException
     */
    public function getAccessTokenIdFromAccessToken(): string
    {
        $accessToken = request()->bearerToken();

        $jti = $this->extractJtiFromAccessToken($accessToken);

        $accessToken = Token::find($jti);

        return $accessToken->id;
    }

    /**
     * Get the user object through the access token in the current request.
     *
     * @param string $accessToken
     * @throws CustomAuthenticationException
     * @return User The user object.
     */
    public function getUserFromAccessToken(string $accessToken): User
    {
        $jti = $this->extractJtiFromAccessToken($accessToken);

        $accessToken = Token::find($jti);

        $userId = $accessToken->user_id;

        return User::findOrFail($userId);
    }

    private function extractJtiFromAccessToken(string $accessToken): string
    {
        $accessTokenParts = explode('.', $accessToken);

        if (count($accessTokenParts) < 2) {
            throw new CustomAuthenticationException('Invalid access token format');
        }

        $accessTokenPayload = json_decode(base64_decode($accessTokenParts[1]));
        $jti = $accessTokenPayload->jti;

        return $jti;
    }
}
