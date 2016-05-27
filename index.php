<?php

use \Slim\Slim;

require 'vendor/autoload.php';

define('ROOT_DIR', __DIR__);

$app = new Slim(array(
    'debug' => true,
    'mode' => 'development',
    'templates.path' => './templates'
));

require './routes/angular.php';

$app->run();