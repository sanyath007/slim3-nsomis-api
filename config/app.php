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
            'charset'   => getenv("DB_CHARSET"), //utf8, tis620
            'collation' => getenv("DB_COLLATE"), //utf8_general_ci, tis620_thai_ci
            'prefix'    => getenv("DB_PREFIX"),
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
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' .getenv("DB_CHARSET"). ' COLLATE ' .getenv("DB_COLLATE")
            ],
        ],
        'person_db' => [
            'driver'    => getenv("DB_PERSON_DRIVER"),
            'host'      => getenv("DB_PERSON_HOST"),
            'database'  => getenv("DB_PERSON_NAME"),
            'username'  => getenv("DB_PERSON_USER"),
            'password'  => getenv("DB_PERSON_PASS"),
            'port'      => getenv("DB_PERSON_PORT"),
            'charset'   => getenv("DB_PERSON_CHARSET"), //utf8, tis620
            'collation' => getenv("DB_PERSON_COLLATE"), //utf8_general_ci, tis620_thai_ci
            'prefix'    => getenv("DB_PERSON_PREFIX"),
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
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' .getenv("DB_PERSON_CHARSET"). ' COLLATE ' .getenv("DB_PERSON_COLLATE")
            ],
        ],
        'pharma_db' => [
            'driver'    => getenv("DB_PHARMA_DRIVER"),
            'host'      => getenv("DB_PHARMA_HOST"),
            'database'  => getenv("DB_PHARMA_NAME"),
            'username'  => getenv("DB_PHARMA_USER"),
            'password'  => getenv("DB_PHARMA_PASS"),
            'port'      => getenv("DB_PHARMA_PORT"),
            'charset'   => getenv("DB_PHARMA_CHARSET"), //utf8, tis620
            'collation' => getenv("DB_PHARMA_COLLATE"), //utf8_general_ci, tis620_thai_ci
            'prefix'    => getenv("DB_PHARMA_PREFIX"),
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
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' .getenv("DB_PHARMA_CHARSET"). ' COLLATE ' .getenv("DB_PHARMA_COLLATE")
            ],
        ],
        'payarrear_db' => [
            'driver'    => getenv("DB_ARREAR_DRIVER"),
            'host'      => getenv("DB_ARREAR_HOST"),
            'database'  => getenv("DB_ARREAR_NAME"),
            'username'  => getenv("DB_ARREAR_USER"),
            'password'  => getenv("DB_ARREAR_PASS"),
            'port'      => getenv("DB_ARREAR_PORT"),
            'charset'   => getenv("DB_ARREAR_CHARSET"), //utf8, tis620
            'collation' => getenv("DB_ARREAR_COLLATE"), //utf8_general_ci, tis620_thai_ci
            'prefix'    => getenv("DB_ARREAR_PREFIX"),
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
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' .getenv("DB_ARREAR_CHARSET"). ' COLLATE ' .getenv("DB_ARREAR_COLLATE")
            ],
        ],
        'escheduling_db' => [
            'driver'    => getenv("DB_SCHEDULING_DRIVER"),
            'host'      => getenv("DB_SCHEDULING_HOST"),
            'database'  => getenv("DB_SCHEDULING_NAME"),
            'username'  => getenv("DB_SCHEDULING_USER"),
            'password'  => getenv("DB_SCHEDULING_PASS"),
            'port'      => getenv("DB_SCHEDULING_PORT"),
            'charset'   => getenv("DB_SCHEDULING_CHARSET"), //utf8, tis620
            'collation' => getenv("DB_SCHEDULING_COLLATE"), //utf8_general_ci, tis620_thai_ci
            'prefix'    => getenv("DB_SCHEDULING_PREFIX"),
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
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' .getenv("DB_SCHEDULING_CHARSET"). ' COLLATE ' .getenv("DB_SCHEDULING_COLLATE")
            ],
        ],
    ]
];