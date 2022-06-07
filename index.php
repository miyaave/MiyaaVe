<?php

use core\router\App;
use core\router\Request;
use core\router\Router;

require 'vendor/autoload.php';
require 'core/inc/bootstrap.php';

spl_autoload_register(function ($class_name) {
	include $class_name . '.php';
});

session_start();

//If we are not in production mode, we will display errors to the web browser.
try {
	if (!App::get('config')['options']['production']) {
		display_errors();
	}
} catch (Exception $e) {
}

//This is where we load the routes from the routes file.
try {
	Router::load('bloc/routes.php')->direct(Request::uri(), Request::method());
} catch (Exception $e) {
	return $e;
}
