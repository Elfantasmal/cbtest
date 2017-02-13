<?php

namespace JiaLeo\Core;

use Illuminate\Support\ServiceProvider;

class CoreProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //加载核心辅助函数
        require_once __DIR__.'/Hepler.php';

        //调试模式
        if(config('app.debug') === true){
            //注册路由
            if (!$this->app->routesAreCached()) {
                require __DIR__ . '/routes/debug.php';
            }
            $debug=new \JiaLeo\Core\Debuger();
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
