<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\AuthController;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\ListUsersRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserDataResource;
use App\Http\Resources\UserRelationshipResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends AuthController
{
    /**
     * Retrieve all available users.
     *
     * @return JsonResponse
     */
    public function index(ListUsersRequest $request): JsonResponse
    {
        $users = User::all();

        $data = [
            'type' => 'users',
            'attributes' => UserDataResource::collection($users),
            'relationships' => new UserRelationshipResource($request->user())
        ];

        return response()->json([
            'data' => $data,
            'meta' => ['http_code' => (string) JsonResponse::HTTP_OK],
        ]);
    }

    /**
     * Delete a specific user.
     *
     * @param DeleteUserRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(DeleteUserRequest $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

     /**
     * Update a specific user.
     *
     * @param UpdateUserRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update($request->validated());

        $data = [
            'type' => 'users',
            'attributes' => new UserDataResource($user),
            'relationships' => new UserRelationshipResource($request->user()),
        ];

        return response()->json([
            'data' => $data,
            'meta' => ['http_code' => (string) JsonResponse::HTTP_OK],
        ]);
    }
}
