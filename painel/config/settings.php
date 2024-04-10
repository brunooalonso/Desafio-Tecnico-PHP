<?php

return [
    'active_connection' => 'pgsql',
    'databases' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'dbname' => 'softexpert',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => 'localhost',
            'port' => 5432,
            'dbname' => 'softexpert',
            'schema' => 'desafio', 
            'username' => 'postgres',
            'password' => '123456789',
            'charset' => 'UTF8',
        ],
    ],
    'mail' => [
        'mail_host' => null,
        'mail_username' => null,
        'mail_password' => null,
        'mail_from' => null,
        'mail_from_name' => null
    ]
];
