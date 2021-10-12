<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => 'icommerceopenpay/v1'], function (Router $router) {
    
    $router->get('/', [
        'as' => 'icommerceopenpay.api.openpay.init',
        'uses' => 'IcommerceOpenpayApiController@init',
    ]);

    $router->post('/processPayment', [
        'as' => 'icommerceopenpay.api.openpay.processPayment',
        'uses' => 'IcommerceOpenpayApiController@processPayment',
    ]);


});