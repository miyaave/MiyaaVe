<?php

use core\router\App;
use core\router\Router;


$router = new Router();

$router->getArray([
    'rest/api/test' => 'TestController@testGet',
    'rest/api/test/{id}' => 'TestController@testGetById'
]);

$router->postArray([
    'rest/api/test' => 'TestController@test'
]);
