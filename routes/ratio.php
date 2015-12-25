<?php

use Bw\Api\Service\Ratio as RatioService;

$app->get('/pickup-ratio', function () use ($app) {
    $ratioService = new RatioService();
    try {
        $ratioService->pickupRatio();
    } catch (\Exception $e) {
        file_put_contents($GLOBALS['root_dir'].'/data/logs/exceptions.log', print_r($e->getMessage(), 1 ) . "\n", FILE_APPEND);
    }
    
});