<?php
namespace core\database;


use core\router\App;
use PDO;
use PDOException;

class Connection
{
    /*
     * This function creates a persistent database connection for the application.
     */
    public static function make($config)
    {
        try {
            return new PDO(
                $config['connection'] . ';dbname=' . $config['name'],
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            App::logError('There was a PDO Exception. Details: ' . $e);
            if (App::get('config')['options']['debug']) {
                header('HTTP/1.0 500 PDO Exception Con');
                return iView('error/500', ['error' => $e->getMessage()]);
            }
            header('HTTP/1.0 500 PDO Exception Con');
            return iView('error/500');
        }
    }
}


?>
