<?php
use Slim\App;
use Slim\Http\Environment;

require 'vendor/autoload.php';

define('ROOT_DIR', __DIR__);

$GLOBALS['root_dir'] = getcwd();
$argv = $GLOBALS['argv'];
array_shift($argv);
$pathInfo = '/' . implode('/', $argv);

$env = Environment::mock(['REQUEST_URI' => '/' . $pathInfo]);

$app = new App(array(
    'debug' => true,
    'mode' => 'development',
    'templates.path' => './templates',
    'environment' => $env
));

require './routes/cron.php';

$app->run();