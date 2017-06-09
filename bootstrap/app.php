<?php

define('DIR', __DIR__);

session_start();

require DIR . '/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDeatails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => 'mysql',
            'database' => 'slim',
            'username' => 'root',
            'password' => 'admin',
            'collation' => 'utf8_unciode_ci',
            'port' => '3306'
        ]
    ],
]);

$container = $app->getContainer();

//Eloquent
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['model'] = function ($container) use ($capsule) {
    return $capsule;
};

$container['config'] = function ($container) {
    $config = file_get_contents(__DIR__ . '/config.json');
    $config = json_decode($config);

    return $config;
};

$container['db'] = function ($container) {
    $db = $container->config->db;

    try
    {
        return  new \PDO("mysql:host=$db->host;port=$db->port;dbname=$db->database", $db->username, $db->password);
    }
    catch (PDOException $e)
    {
        return $e->getMessage();
    }
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(DIR . '/../resources/views', [
        'cache' => false,
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    return $view;
};

$container['HomeController'] = function ($container) { return new \App\Controllers\HomeController($container); };
$container['AuthController'] = function ($container) { return new \App\Controllers\AuthController($container); };
$container['UserController'] = function ($container) { return new \App\Controllers\UserController($container); };


require DIR . '/../app/routes.php';

