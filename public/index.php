<?php

define('APP_ROOT_DIR', __DIR__ . '/../');

require APP_ROOT_DIR . 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT_DIR);
$dotenv->load();

$config = require APP_ROOT_DIR . 'config/app.php';

$app = new Slim\App($config);

$container = $app->getContainer();

$capsule = new Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function($c) use ($capsule) {
    return $capsule;
};

$container['pdo'] = function ($c) {
    try {
        $conStr = $c['settings']['db'];

        return new PDO($conStr['driver']. ":host=" .$conStr['host']. ";dbname=" .$conStr['database'], $conStr['username'], $conStr['password'], $conStr['options']);
    }
    catch(\Exception $ex) {
        return $ex->getMessage();
    }   
};

$app->add(new Slim\Middleware\JwtAuthentication([
    'path'      => '/api',
    'secret'    => getenv("JWT_SECRET")
]));

$container['HomeController'] = function($c) {
    return new App\Controllers\HomeController($c);
};

$container['UserController'] = function($c) {
    return new App\Controllers\UserController($c);
};

$container['IpController'] = function($c) {
    return new App\Controllers\IpController($c);
};

$container['OpController'] = function($c) {
    return new App\Controllers\OpController($c);
};

$container['ErController'] = function($c) {
    return new App\Controllers\ErController($c);
};

$container['OrController'] = function($c) {
    return new App\Controllers\OrController($c);
};

require APP_ROOT_DIR . 'src/routes.php';

$app->run();
