<?php

namespace Tests\Unit\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\RegisterController;
use App\Http\Requests\RegisterRequest;
use App\Services\AccessTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

// Registratie unit test
class RegisterControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    private $accessTokenServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accessTokenServiceMock = Mockery::mock(AccessTokenService::class);
    }

    public function test_register_returns_json_response_with_access_token()
    {
        // Arrange
        $controller = new RegisterController($this->accessTokenServiceMock);
        $registerRequest = RegisterRequest::create(
            '/api/register',
            'POST',
            [
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
                'password' => 'password123',
            ]
        );

        $accessTokenResponse = [
            'access_token' => 'access_token',
            'expires_in' => 3600,
            'refresh_token' => 'refresh_token',
        ];

        $this->accessTokenServiceMock
            ->shouldReceive('fetchToken')
            ->once()
            ->with(
                [
                    'name' => 'John Doe',
                    'email' => 'johndoe@example.com',
                    'password' => 'password123',
                ],
                'operator'
            )
            ->andReturn($accessTokenResponse);

        DB::beginTransaction();

        // Act
        $response = $controller->register($registerRequest);

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
        $this->assertEquals('John Doe', $responseData['included'][0]['attributes']['name']);
        $this->assertEquals('johndoe@example.com', $responseData['included'][0]['attributes']['email']);
        $this->assertEquals('operator', $responseData['included'][0]['attributes']['role']);

        DB::rollBack();
    }
}
