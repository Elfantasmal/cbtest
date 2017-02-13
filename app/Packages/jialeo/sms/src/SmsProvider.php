<?php

namespace JiaLeo\Sms;

use Illuminate\Support\ServiceProvider;

class SmsProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('sms', function () {
            return new Sms;
        });

    }

    /**
     * 获取由提供者提供的服务.
     *
     * @return array
     */
    public function provides()
    {
        return ['sms'];
    }
}
