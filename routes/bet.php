<?php

use Bw\Api\Service\Bet as BetService;

$app->get('/generate-bets', function () use ($app) {
    $betService = new BetService();
    
    try {
        $betService->generateBets();
        echo 'Finished!';
    } catch (\Exception $e) {
        file_put_contents($GLOBALS['root_dir'].'/data/logs/exceptions.log', print_r($e->getMessage(), 1 ) . "\n", FILE_APPEND);
    }
    
});

$app->get('/calculate-bets', function () use ($app) {
    $betService = new BetService();
    
    try {
        $betService->calculateBets();
        echo 'Finished!';
    } catch (\Exception $e) {
        file_put_contents($GLOBALS['root_dir'].'/data/logs/exceptions.log', print_r($e->getMessage(), 1 ) . "\n", FILE_APPEND);
    }    
});

$app->get('/get-own-users', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $betService = new BetService();
    
    try {
        $result = $betService->getOwnUsers();
        echo json_encode($result, JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        echo '<pre>' . var_dump($e) . '</pre>'; die();
    }    
});

$app->get('/get-success-users', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $betService = new BetService();
    
    try {
        $result = $betService->getSuccessUsers();
        echo json_encode($result, JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        echo '<pre>' . var_dump($e) . '</pre>'; die();
    }    
});

$app->get('/get-failure-users', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $betService = new BetService();
    
    try {
        $result = $betService->getFailureUsers();
        echo json_encode($result, JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        echo '<pre>' . var_dump($e) . '</pre>'; die();
    }    
});

$app->get('/bets/:userId', function ($userId) use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $betService = new BetService();
    
    try {
        $result = $betService->getUserBets($userId);
        echo json_encode($result, JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        echo '<pre>' . var_dump($e) . '</pre>'; die();
    }    
});