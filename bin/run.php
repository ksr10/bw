<?php

use \Slim\Slim;
use \Slim\Environment;

$rootDir = dirname(__DIR__);
chdir($rootDir);

$GLOBALS['root_dir'] = $rootDir;
$argv = $GLOBALS['argv'];
array_shift($argv);
$pathInfo = '/' . implode('/', $argv);

require 'vendor/autoload.php';

$app = new Slim(array(
    'debug' => true,
    'mode' => 'development',
    'templates.path' => './templates'
));

$app->environment = Environment::mock(array('PATH_INFO' => $pathInfo));

$app->notFound(function () use ($app) {
    $url = $app->environment['PATH_INFO'];
    echo "Error: Cannot route to $url";
    $app->stop();
});

$app->error(function (\Exception $e) use ($app) {
    echo $e;
    $app->stop();
});

require './routes/ratio.php';

$app->run();