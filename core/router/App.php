<?php

namespace core\router;

use core\logger\LogFile;
use core\logger\Logger;
use Exception;

include 'core/logger/LogFile.php';

class App
{

    protected static $registry = [];

    public static function bind($key, $value)
    {
        static::$registry[$key] = $value;
    }

    public static function get($key)
    {
        if (!array_key_exists($key, static::$registry)) {
            throw new Exception("No {$key} is bound in the container.");
        }
        return static::$registry[$key];
    }

    public static function DB()
    {
        try {
            return static::get('database');
        } catch (Exception $e) {
            return null;
        }
    }

    public static function Config()
    {
        try {
            return static::get('config');
        } catch (Exception $e) {
            return null;
        }
    }

    public static function logInfo($data, Logger $logger = null)
    {
        $logger = $logger ?: new LogFile();
        return $logger->info($data);
    }

    public static function logError($data, Logger $logger = null)
    {
        $logger = $logger ?: new LogFile();
        return $logger->error($data);
    }
}
