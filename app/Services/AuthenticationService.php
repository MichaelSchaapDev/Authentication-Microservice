<?php

namespace App\Services;

use App\Exceptions\InternalServerErrorException;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    /**
     * Authenticates a user using the given credentials.
     * 
     * @param array $credentials The user's login credentials (email and password).
     * @throws InternalServerErrorException If there was an error querying the database.
     * @throws InvalidCredentialsException If the provided credentials are invalid.
     * @return User The authenticated user.
     */
    public function authenticate(array $credentials): User
    {
        try {
            $user = User::where('email', $credentials['email'])->first();
        } catch (QueryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new InvalidCredentialsException();
        }

        return $user;
    }
}
