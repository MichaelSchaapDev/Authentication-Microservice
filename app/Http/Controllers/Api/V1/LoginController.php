<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\InternalServerErrorException;
use App\Http\Controllers\AuthController;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\TokenResource;
use App\Http\Resources\UserRelationshipResource;
use App\Http\Resources\UserDataResource;
use App\Services\AccessTokenService;
use App\Services\AuthenticationService;
use Illuminate\Http\JsonResponse;

class LoginController extends AuthController
{
    public function __construct(
        private AccessTokenService $accessTokenService,
        private AuthenticationService $authenticationService
    ) {
    }

    /**
     * Authenticate a user and return an access token.
     *
     * @param LoginRequest $request
     *
     * @throws InternalServerErrorException
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);
        $user = $this->authenticationService->authenticate($credentials);
        $userRole = $user->getRole();

        $accessTokenResponse = $this->accessTokenService->fetchToken($credentials, $userRole);

        $data = [
            'type' => 'tokens',
            'attributes' => new TokenResource($accessTokenResponse),
            'relationships' => new UserRelationshipResource($user)
        ];

        return response()->json([
            'data' => $data,
            'included' => [ new UserDataResource($user) ],
            'meta' => [ 'http_code' => (string) JsonResponse::HTTP_OK ],
        ]);
    }
}
