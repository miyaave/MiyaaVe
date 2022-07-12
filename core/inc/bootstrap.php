<?php

include 'core/router/App.php';
include 'core/database/QueryBuilder.php';
include 'core/database/Connection.php';

use core\database\Connection;
use core\database\QueryBuilder;
use core\router\App;


require 'helpers.php';

if (getenv('DATABASE_NAME') != null) {

    App::bind('config', require 'config.php');

    try {
        App::bind('database', new QueryBuilder(
            Connection::make(App::get('config')['database'])
        ));
    } catch (Exception $e) {
        return null;
    }
}
?>
