<?php

namespace Zxg321\ZCms;

use Illuminate\Support\ServiceProvider;

class ZcmsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'zcms');
    }
    public function register()
    {
        # code...
    }
}
