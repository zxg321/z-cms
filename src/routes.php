<?php
$attributes = [
    'prefix'     => 'admin',
    'namespace'  => 'Zxg321\Zcms\Controllers',
    'middleware' => config('admin.route.middleware'),
];

Route::group($attributes, function ($router) {

    $router->group([], function ($router) {
        $router->resource('zcms/category', 'CategoryController');
        $router->resource('zcms/content', 'ContentController');
    });

});