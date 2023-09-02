<?php

namespace App\Providers;

use App\Services\AccessTokenService;
use App\Services\RefreshTokenService;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\AuthorizationServer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $configPath = base_path('config/passport.php');
        $this->mergeConfigFrom($configPath, 'passport');

        $this->app->bind(AccessTokenController::class, function ($app) {
            $server = $app->make(AuthorizationServer::class);
            $tokens = $app->make(TokenRepository::class);
            return new AccessTokenController($server, $tokens);
        });

        $this->app->bind(AccessTokenService::class, function ($app) {
            $accessTokenController = $app->make(AccessTokenController::class);
            $clientId = config('passport.client_id');
            $clientSecret = config('passport.client_secret');
            return new AccessTokenService($accessTokenController, $clientId, $clientSecret);
        });

        $this->app->bind(RefreshTokenService::class, function ($app) {
            $accessTokenController = $app->make(AccessTokenController::class);
            $clientId = config('passport.client_id');
            $clientSecret = config('passport.client_secret');
            return new RefreshTokenService($accessTokenController, $clientId, $clientSecret);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
