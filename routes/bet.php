<?php

use Bw\Api\Service\Bet as BetService;

$app->get('/generate-bets', function () use ($app) {
    $betService = new BetService();
    
    try {
        $betService->generateBets();
    } catch (\Exception $e) {
        file_put_contents($GLOBALS['root_dir'].'/data/logs/exceptions.log', print_r($e->getMessage(), 1 ) . "\n", FILE_APPEND);
    }
    
});