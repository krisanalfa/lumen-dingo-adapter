<?php

namespace Zeek\LumenDingoAdapter\Providers;

use JWTAuth;
use JWTFactory;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Cookie\CookieJar;
use Illuminate\Auth\AuthManager;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Cache\MemcachedConnector;
use Tymon\JWTAuth\JWTAuth as TymonJWTAuth;
use Tymon\JWTAuth\Middleware\RefreshToken;
use Tymon\JWTAuth\Providers\JWT\JWTInterface;
use Tymon\JWTAuth\Middleware\GetUserFromToken;
use Illuminate\Session\SessionServiceProvider;
use Tymon\JWTAuth\Providers\JWTAuthServiceProvider;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuthFacade;
use Tymon\JWTAuth\Facades\JWTFactory as JWTFactoryFacade;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;

class LumenJWTServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        // JWTAuth Dependency
        $this->registerJWTAuthDependency();

        // JWT itself
        $this->registerJWTServiceProvider();

        // Middleware route registration
        $this->registerJwtAuthMiddleware();
    }

    /**
     * Here we do some internal function to fulfill JWTAuth dependency.
     */
    protected function registerJWTAuthDependency()
    {
        $this->registerRequest();

        $this->registerRoutingAlias();

        $this->registerSessionServiceProvider();

        $this->registerCookieComponent();

        $this->registerCacheServiceProvider();

        $this->registerAuthManagerAlias();
    }
    
    protected function registerRequest()
    {
        $this->app->instance(Request::class, Request::capture());
    }

    /**
     * Let's alias this route. Since Lumen doesn't use Illuminate Route component,
     * we can safely using base response instance to 'hack' JWT Auth dependency.
     */
    protected function registerRoutingAlias()
    {
        $this->app->singleton(
            ResponseFactoryContract::class,
            ResponseFactory::class
        );
    }

    /**
     * Register session to application.
     */
    protected function registerSessionServiceProvider()
    {
        $this->registerSessionManager();

        $this->registerSessionStore();
    }

    protected function registerSessionManager()
    {
        $this->loadComponent(
            SessionManager::class,
            'SessionManager'
        );
    }

    protected function registerSessionStore()
    {
        $this->loadComponent(
            Store::class,
            'SessionStore'
        );
    }

    /**
     * Load session manager component.
     *
     * @return Closure
     */
    protected function loadSessionManagerComponent()
    {
        return function ($app) {
            return $app->loadComponent(
                'session',
                SessionServiceProvider::class
            );
        };
    }

    /**
     * Load session store component.
     *
     * @return Closure
     */
    protected function loadSessionStoreComponent()
    {
        return function ($app) {
            return $app->loadComponent(
                'session',
                SessionServiceProvider::class,
                'session.store'
            );
        };
    }

    /**
     * Register cookie to application.
     */
    protected function registerCookieComponent()
    {
        $this->loadComponent(
            CookieJar::class,
            'CookieJar'
        );
    }

    /**
     * Load cookie component.
     *
     * @return Closure
     */
    protected function loadCookieJarComponent()
    {
        return function ($app) {
            return new CookieJar();
        };
    }

    /**
     * Register cache to application.
     */
    protected function registerCacheServiceProvider()
    {
        $this->registerCacheManager();

        $this->registerMemcachedConnector();
    }

    /**
     * Register cache manager.
     */
    protected function registerCacheManager()
    {
        $this->loadComponent(
            CacheManager::class,
            'CacheManager'
        );
    }

    /**
     * Register memcached connector.
     */
    protected function registerMemcachedConnector()
    {
        $this->loadComponent(
            MemcachedConnector::class,
            'MemcachedConnector'
        );
    }

    /**
     * Load cache manager component.
     *
     * @return Closure
     */
    protected function loadCacheManagerComponent()
    {
        return function ($app) {
            $app->configure('cache');

            return new CacheManager($app);
        };
    }

    /**
     * Load memcached connector component.
     *
     * @return Closure
     */
    protected function loadMemcachedConnectorComponent()
    {
        return function ($app) {
            return new MemcachedConnector();
        };
    }

    /**
     * Register auth manager to application.
     */
    protected function registerAuthManagerAlias()
    {
        $this->app->alias('auth', AuthManager::class);
    }

    /**
     * Register JWTAuth to application.
     */
    protected function registerJWTServiceProvider()
    {
        $this->registerJwtAuth();

        $this->registerJWTFacades();
    }

    /**
     * Register JWTAuth resolver.
     */
    protected function registerJwtAuth()
    {
        $this->registerBaseJWTAuth();

        $this->registerJwtAuthProvider();
    }

    /**
     * Register JWTAuth base instance resolver.
     */
    protected function registerBaseJWTAuth()
    {
        $this->loadComponent(
            TymonJWTAuth::class,
            'JWTAuth'
        );
    }

    /**
     * Register JWTAuthProvider instance resolver.
     */
    protected function registerJwtAuthProvider()
    {
        $this->loadComponent(
            JWTInterface::class,
            'JWTAuthProvider'
        );
    }

    /**
     * Load JWT Auth component.
     *
     * @return Closure
     */
    protected function loadJWTAuthComponent()
    {
        return function ($app) {
            return $app->loadComponent(
                'jwt',
                JWTAuthServiceProvider::class,
                TymonJWTAuth::class
            );
        };
    }

    /**
     * Load JWT Auth component.
     *
     * @return Closure
     */
    protected function loadJWTAuthProviderComponent()
    {
        return function ($app) {
            return $app->loadComponent(
                'jwt',
                JWTAuthServiceProvider::class,
                JWTInterface::class
            );
        };
    }

    /**
     * Register JWTAuth facades.
     */
    protected function registerJWTFacades()
    {
        if ($this->shouldRegisterFacades()) {
            class_alias(JWTAuthFacade::class, JWTAuth::class);
            class_alias(JWTFactoryFacade::class, JWTFactory::class);
        }
    }

    /**
     * Determine if we should register JWT Auth facades.
     *
     * @return bool
     */
    protected function shouldRegisterFacades()
    {
        return $this->isUsingFacade() === true
            && $this->facadeHasNotBeenRegistered() === true;
    }

    /**
     * Determine if application is using facade.
     *
     * @return bool
     */
    protected function isUsingFacade()
    {
        return Facade::getFacadeApplication() !== null;
    }

    /**
     * Determine if JWT Auth facade has not been registered.
     *
     * @return bool
     */
    protected function facadeHasNotBeenRegistered()
    {
        return class_exists(JWTAuthFacade::class) === false;
    }

    /**
     * Register JWTAuth middleware.
     */
    protected function registerJwtAuthMiddleware()
    {
        $this->app->routeMiddleware([
            'jwt.auth' => GetUserFromToken::class,
            'jwt.refresh' => RefreshToken::class,
        ]);
    }

    /**
     * Load component by given bindings an name resolver.
     *
     * @param array  $bindings
     * @param string $name
     */
    protected function loadComponent($bindings, $name)
    {
        $aliases = array_values($bindings);
        $abstracts = array_keys($bindings);
        
        foreach ($abstracts as $index => $abstract) {
            $this->app->singleton(
                $abstract,
                $this->{"load{$name}Component"}()
            );
            
            $this->app->alias(
                $abstract,
                $aliases[$index]
            );
        }
    }
}
