<?php

namespace Zeek\LumenDingoAdapter\Providers;

use Illuminate\Support\ServiceProvider;
use Dingo\Api\Provider\LumenServiceProvider;

class LumenDingoAdapterServiceProvider extends ServiceProvider
{
    /**
     * Boot the application services.
     *
     * @return void
     */
    public function boot ()
    {
        $this->configure('auth');
        $this->configure('session');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // JWTAuth
        $this->app->register(LumenJWTServiceProvider::class);

        // Dingo
        $this->app->register(LumenServiceProvider::class);

        // Configure our JWT for Dingo
        $this->app->register(DingoJWTDriverServiceProvider::class);
    }

    protected function configure($name)
    {
        $this->app->make('config')->set($name, require dirname(__DIR__) . "/config/{$name}.php");
    }
}
