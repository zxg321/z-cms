<?php

namespace Zxg321\Zcms;

use Illuminate\Support\ServiceProvider;

class ZcmsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'zcms');
        $this->publishes([
            __DIR__.'/../config/zcms.php' => config_path('zcms.php')
        ], 'zcms-config');
        $this->publishes([
            __DIR__.'/Database/migrations/' => database_path('migrations')
        ], 'zcms-migrations');
    }
    public function register()
    {
        # code...
    }
}
