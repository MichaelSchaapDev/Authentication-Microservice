<?php

namespace Tests\Unit\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Requests\LoginRequest;
use App\Services\AccessTokenService;
use App\Services\AuthenticationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Tests\TestCase;

// Unit Test
class LoginControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    private AccessTokenService|LegacyMockInterface|MockInterface $accessTokenService;
    private AuthenticationService|LegacyMockInterface|MockInterface $authenticationService;
    private LoginController $loginController;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the AccessTokenService and AuthenticationService classes
        $this->accessTokenService = Mockery::mock(AccessTokenService::class);
        $this->authenticationService = Mockery::mock(AuthenticationService::class);

        // Instantiate the LoginController class with the mocked dependencies
        $this->loginController = new LoginController(
            $this->accessTokenService,
            $this->authenticationService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * Test the login method of the LoginController class.
     */
    public function test_that_login_works(): void
    {
        // Arrange
        $loginRequest = Mockery::mock(LoginRequest::class);
        $loginRequest
            ->shouldReceive('only')
            ->andReturn([
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

        $user = new \App\Models\User();
        $user->id = 1;
        $user->name = 'Test User';
        $user->email = 'test@example.com';
        $user->password = bcrypt('password');
        $user->role = 'operator';

        $accessTokenResponse = [
            'access_token' => 'abc123',
            'expires_in' => 3600,
            'refresh_token' => 'def456',
        ];

        $this->authenticationService
            ->shouldReceive('authenticate')
            ->with([
                'email' => 'test@example.com',
                'password' => 'password',
            ])
            ->andReturn($user);

        $this->accessTokenService
            ->shouldReceive('fetchToken')
            ->with([
                'email' => 'test@example.com',
                'password' => 'password',
            ], 'operator')
            ->andReturn($accessTokenResponse);
            
        // Act
        $response = $this->loginController->login($loginRequest);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('type', $responseData['data']);
        $this->assertArrayHasKey('attributes', $responseData['data']);
        $this->assertArrayHasKey('relationships', $responseData['data']);
        $this->assertEquals('tokens', $responseData['data']['type']);
        $this->assertEquals($accessTokenResponse['access_token'], $responseData['data']['attributes']['access_token']);
        $this->assertEquals($accessTokenResponse['refresh_token'], $responseData['data']['attributes']['refresh_token']);
        $this->assertArrayHasKey('user', $responseData['data']['relationships']);
        $this->assertEquals('users', $responseData['data']['relationships']['user']['data']['type']);
        $this->assertEquals('1', $responseData['data']['relationships']['user']['data']['id']);
        $this->assertEquals('Test User', $responseData['included'][0]['attributes']['name']);
        $this->assertEquals('test@example.com', $responseData['included'][0]['attributes']['email']);
        $this->assertEquals('operator', $responseData['included'][0]['attributes']['role']);
    }
}
