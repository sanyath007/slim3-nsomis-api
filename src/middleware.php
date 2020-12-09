<?php

use Tuupola\Middleware\HttpBasicAuthentication;

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

$container['logger'] = function($c) {
    $logger = new Monolog\Logger('My_logger');
    $file_handler = new Monolog\Handler\StreamHandler('../logs/app.log');
    $logger->pushHandler($file_handler);

    return $logger;
};

$container['jwt'] = function($c) {
    return new StdClass;
};

$app->add(new Slim\Middleware\JwtAuthentication([
    "path"          => '/api',
    "logger"        => $container['logger'],
    "passthrough"   => ["/test"],
    "secret"        => getenv("JWT_SECRET"),
    "callback"      => function($req, $res, $args) use ($container) {
        $container['jwt'] = $args['decoded'];
    },
    "error"         => function($req, $res, $args) {
        $data["status"] = "0";
        $data["message"] = $args["message"];
        $data["data"] = "";
        
        return $res
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
]));

$container['HomeController'] = function($c) {
    return new App\Controllers\HomeController($c);
};

$container['LoginController'] = function($c) {
    return new App\Controllers\Auth\LoginController($c);
};

$container['DashboardController'] = function($c) {
    return new App\Controllers\DashboardController($c);
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