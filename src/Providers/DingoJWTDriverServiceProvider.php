<?php

namespace Zeek\LumenDingoAdapter\Providers;

use Dingo\Api\Auth\Auth;
use Tymon\JWTAuth\JWTAuth;
use Dingo\Api\Auth\Provider\JWT;
use Illuminate\Support\ServiceProvider;

class DingoJWTDriverServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make(Auth::class)->extend('jwt', function ($app) {
            return new JWT($app->make(JWTAuth::class));
        });
    }
}
