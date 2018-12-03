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

        // Session provider
        $this->registerCookieComponent();
        $this->registerIlluminateSession();




        // JWTAuth
        $this->app->register(LumenJWTServiceProvider::class);

        // Dingo
        $this->app->register(LumenServiceProvider::class);

        // Configure our JWT for Dingo
        $this->app->register(DingoJWTDriverServiceProvider::class);
    }

    /**
     * Register the illuminate service provider if it is not registered.
     *
     * @return void
     */
    protected function registerIlluminateSession(){
        if (!isset($this->app['session.store'])) {
            if(!$this->app['config']->has('session.driver')) {
                $this->app['config']->set('session.driver', 'file');
            }
            $this->app->register(\Illuminate\Session\SessionServiceProvider::class);
        }
    }
    protected function registerCookieComponent()
    {
        $app = $this->app;
        $this->app->singleton('cookie', function () use ($app) {
            return $app->loadComponent('session', 'Illuminate\Cookie\CookieServiceProvider', 'cookie');
        });

        $this->app->bind('Illuminate\Contracts\Cookie\QueueingFactory', 'cookie');
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
