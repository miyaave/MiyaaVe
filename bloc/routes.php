<?php

use core\router\Router;


$router = new Router();

$router->getArray([
    'test' => 'TestController@test'
    #'rest/api/test/{id}' => 'TestController@test'
]);

$router->postArray([
    'rest/api/test' => 'TestController@test'
]);
