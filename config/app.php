<?php

return [
    'settings' => [
        'addContentLengthHeader' => false,
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true,
        'db' => [
            'driver'    => getenv("DB_DRIVER"),
            'host'      => getenv("DB_HOST"),
            'database'  => getenv("DB_NAME"),
            'username'  => getenv("DB_USER"),
            'password'  => getenv("DB_PASS"),
            'port'      => getenv("DB_PORT"),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'options' => [
                // Turn off persistent connections
                PDO::ATTR_PERSISTENT => false,
                // Enable exceptions
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // Emulate prepared statements
                PDO::ATTR_EMULATE_PREPARES => true,
                // Set default fetch mode to array
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Set character set
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES tis620 COLLATE tis620_thai_ci'
            ],
        ],
    ]
];