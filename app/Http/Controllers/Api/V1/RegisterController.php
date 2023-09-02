<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\InternalServerErrorException;
use App\Exceptions\UserCreationException;
use App\Http\Controllers\AuthController;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\TokenResource;
use App\Http\Resources\UserRelationshipResource;
use App\Http\Resources\UserDataResource;
use App\Models\User;
use App\Services\AccessTokenService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class RegisterController extends AuthController
{
    private AccessTokenService $accessTokenService;

    public function __construct(AccessTokenService $accessTokenService)
    {
        $this->accessTokenService = $accessTokenService;
    }

    /**
     * Register a new user and issue an access token.
     *
     * @param  RegisterRequest  $request The HTTP request containing the user's name, email and password.
     *
     * @return JsonResponse The HTTP response containing the access token.
     *
     * @throws InternalServerErrorException
     */
    public function register(RegisterRequest $request): JsonResponse
    {

        $credentials = $request->only(['name', 'email', 'password']);

        $user = $this->createUser($credentials);

        $accessTokenResponse = $this->accessTokenService->fetchToken($credentials, 'operator');

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

    /**
     * Create a new user in the database.
     *
     * @param  array  $credentials An associative array containing the name, email and password of the user.
     *
     * @return User The created user.
     *
     * @throws UserCreationException
     */
    private function createUser(array $credentials): User
    {
        $hashedPassword = bcrypt($credentials['password']);

        try {
            return User::create([
                'name'     => $credentials['name'],
                'email'    => $credentials['email'],
                'password' => $hashedPassword,
                'role'     => 'operator',
            ]);
        } catch (QueryException $exception) {
            throw new UserCreationException($exception->getMessage());
        }
    }
}
