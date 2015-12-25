<?php

$app->get('/', function () use ($app) {
    $app->render('angular/index.php');
});