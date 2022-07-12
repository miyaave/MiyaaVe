<?php

namespace core\logger;

use RuntimeException;

include 'Logger.php';

class LogFile implements Logger
{

    public function info($data)
    {
        return $this->log($data, "info.log");
    }

    private function log($data, $filename = "log.log")
    {
        if (!file_exists("logs/") && (!mkdir("logs/", 0777, true) && !is_dir($filename))) {
            throw new RuntimeException(sprintf('Folder "%s" was not created', $filename));
        }
        return file_put_contents("logs/" . $filename, date("Y-m-d h:i:sa") . " " . $data . "\n", FILE_APPEND);
    }

    public function error($data)
    {
        return $this->log($data, "error.log");
    }
}
