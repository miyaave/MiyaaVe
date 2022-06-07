<?php

use core\inc\DotEnv;

$dir = $_SERVER['DOCUMENT_ROOT'];

(new DotEnv($dir . '/.env'))->load();

return [
    'database' => [
        'name' =>  getenv('DATABASE_NAME'),
        'username' => getenv('DATABASE_USERNAME'),
        'password' => getenv('DATABASE_PASSWORD'),
        'connection' => 'mysql:host='.getenv('DATABASE_HOST'),
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ],
    'options' => [
        'debug' => getenv('DEBUG'),
        'production' => getenv('PRODUCTION'),
        'array_routing' => getenv('ARRAY_ROUTING')
    ]
];