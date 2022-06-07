<?php

namespace core\logger;


interface Logger
{

    public function info($data);

    public function error($data);

}