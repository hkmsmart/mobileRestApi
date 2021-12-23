<?php

$router->group(['prefix' => 'api','middleware' => 'auth'], function () use ($router) {
    $router->post('purchase','AuthController@purchase');
    $router->post('check','AuthController@check');
});

$router->group(['prefix' => 'api'],function () use ($router) {
    $router->post('register','AuthController@register');
    $router->post('login','AuthController@login');
});
