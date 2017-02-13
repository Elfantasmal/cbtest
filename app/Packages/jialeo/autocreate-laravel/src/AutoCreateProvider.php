<?php

namespace JiaLeo\AutoCreate;

use Illuminate\Support\ServiceProvider;

class AutoCreateProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                'JiaLeo\AutoCreate\Model',
                'JiaLeo\AutoCreate\ModelDoc',
                'JiaLeo\AutoCreate\Controller',
                'JiaLeo\AutoCreate\Logic',
                'JiaLeo\AutoCreate\Module'
            ]);
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
    }
}
