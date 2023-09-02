<?php

namespace Tests\Feature\Services;

use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use App\Services\AuthenticationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthenticationService $authenticationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticationService = new AuthenticationService();
    }

    /**
     * Test if the user is authenticated with valid credentials.
     *
     * @return void
     */
    public function test_it_authenticates_a_user_with_valid_credentials(): void
    {
        $password = 'password';
        $user = User::factory()->create([
            'password' => Hash::make($password)
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => $password,
        ];

        $authenticatedUser = $this->authenticationService->authenticate($credentials);

        $this->assertEquals($user->id, $authenticatedUser->id);
    }

    /**
     * Test if an InvalidCredentialsException is thrown when attempting to authenticate with invalid credentials.
     *
     * @return void
     */
    public function test_it_throws_an_exception_when_attempting_to_authenticate_with_invalid_credentials(): void
    {
        $this->expectException(InvalidCredentialsException::class);

        $password = 'password';
        User::factory()->create([
            'password' => Hash::make($password)
        ]);

        $credentials = [
            'email' => 'invalid-email@example.com',
            'password' => $password,
        ];

        $this->authenticationService->authenticate($credentials);
    }
}
