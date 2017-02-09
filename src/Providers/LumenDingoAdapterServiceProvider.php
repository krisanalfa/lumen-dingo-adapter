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

    /**
     * Configure provider.
     *
     * @param string $name
     *
     * @return void
     */
    protected function configure($name)
    {
        $path = $this->app->basePath("config/{$name}.php");

        if (!is_readable($path)) {
            $path = dirname(__DIR__) . "/config/{$name}.php";
        }

        $this->app->make('config')->set($name, require $path);
    }
}
