<?php

use \Slim\Slim;

require 'vendor/autoload.php';

define('ROOT_DIR', __DIR__);

$app = new Slim(array(
    'debug' => true,
    'mode' => 'development',
    'templates.path' => './templates'
));

$GLOBALS['root_dir'] = getcwd();

require './routes/angular.php';
require './routes/bet.php';

$app->run();

if ($isXHR) {
    //$app->response->headers->set('Content-Type', 'application/json');
}