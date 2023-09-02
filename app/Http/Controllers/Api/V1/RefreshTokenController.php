<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\AuthController;
use App\Http\Requests\RefreshTokenRequest;
use App\Http\Resources\TokenResource;
use App\Http\Resources\UserRelationshipResource;
use App\Http\Resources\UserDataResource;
use App\Services\RefreshTokenService;
use Illuminate\Http\JsonResponse;

class RefreshTokenController extends AuthController
{
    /**
     * The refresh token service instance.
     *
     * @var RefreshTokenService
     */
    private $refreshTokenService;

    public function __construct(RefreshTokenService $refreshTokenService)
    {
        $this->refreshTokenService = $refreshTokenService;
    }

    /**
     * Refresh the access token using the provided refresh token.
     *
     * @param RefreshTokenRequest $request The refresh token request instance.
     * @return JsonResponse The JSON response containing the new access token and refresh token.
     */
    public function refresh(RefreshTokenRequest $request, AuthController $authController): JsonResponse
    {
        $refreshToken = $request->input('refresh_token');
        $refreshTokenResponse = $this->refreshTokenService->refreshAccessToken($refreshToken);

        $user = $authController->getUserFromAccessToken($refreshTokenResponse["access_token"]);

        $data = [
            'type' => 'tokens',
            'attributes' => new TokenResource($refreshTokenResponse),
            'relationships' => new UserRelationshipResource($user)
        ];

        return response()->json([
            'data' => $data,
            'included' => [new UserDataResource($user)],
            'meta' => ['http_code' => (string) JsonResponse::HTTP_OK],
        ]);
    }
}
