<?php

namespace JiaLeo\Jwt;

use Illuminate\Support\ServiceProvider;

class JwtAuthProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //注册路由
        if (!$this->app->routesAreCached()) {
            require __DIR__ . '/routes.php';
        }

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('jwt', function () {
            return new JwtAuth;
        });

    }
}
