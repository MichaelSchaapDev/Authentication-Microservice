<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\AuthController;
use App\Http\Requests\PasswordResetRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends AuthController
{
    public function update(PasswordResetRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'errors' => [
                    'current_password' => [
                        'The provided password does not match your current password.'
                    ]
                ]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully.',
            'meta' => ['http_code' => (string) JsonResponse::HTTP_OK],
        ]);
    }
}
