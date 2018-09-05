z-cms for laravel
===============================
```
$ laravel new z-cms
$ php artisan vendor:publish --provider="Encore\Admin\AdminServiceProvider"
$ php artisan admin:install
```
修改配置 .env 文件 数据库配置以及缓存等配置
修改 config/app.php 中时区 
'timezone' => env('TIMEZONE','Asia/Shanghai'),
语言设置 
'locale' => 'zh-CN',
'fallback_locale' => 'zh-CN',

License
------------
Licensed under [The MIT License (MIT)](LICENSE).
