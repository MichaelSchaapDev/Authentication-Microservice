<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define the expiration time for access tokens as 30 minutes
        Passport::tokensExpireIn(now()->addMinutes(30));

        // Define the expiration time for refresh tokens as 4 hours
        Passport::refreshTokensExpireIn(now()->addHours(10));

        Passport::enableImplicitGrant();

        Passport::tokensCan([
            'operator' => 'Access limited set of resources',
            'admin' => 'Access all resources',
            'manager' => 'Access all resources and manage admins'
        ]);

        Passport::setDefaultScope([
            'operator',
        ]);
    }
}
