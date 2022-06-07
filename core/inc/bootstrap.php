<?php

include 'core/router/App.php';
include 'core/database/QueryBuilder.php';
include 'core/database/Connection.php';

use core\database\QueryBuilder;
use core\router\App;
use core\database\Connection;


require 'helpers.php';

App::bind('config', require 'config.php');

try {
    App::bind('database', new QueryBuilder(
        Connection::make(App::get('config')['database'])
    ));
} catch (Exception $e) {
}

?>
