<?php

use \Slim\Slim;

require 'vendor/autoload.php';

$app = new Slim(array(
    'debug' => true,
    'mode' => 'development',
    'templates.path' => './templates'
));

require './routes/angular.php';
require './routes/ratio.php';

$app->run();