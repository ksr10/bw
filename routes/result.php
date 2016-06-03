<?php

use Bw\Api\Service\Result as ResultService;

$app->get('/pickup-result', function () use ($app) {
    $resultService = new ResultService();
    
    try {
        $resultService->pickupResult();
    } catch (\Exception $e) {
        file_put_contents($GLOBALS['root_dir'].'/data/logs/exceptions.log', print_r($e->getMessage(), 1 ) . "\n", FILE_APPEND);
    }
    
});