<?php

use Bw\Api\Service\Rule as RuleService;

$app->get('/generate-users', function () use ($app) {
    $ruleService = new RuleService();
    
    try {
        $ruleService->generateUsers();
    } catch (\Exception $e) {
        file_put_contents($GLOBALS['root_dir'].'/data/logs/exceptions.log', print_r($e->getMessage(), 1 ) . "\n", FILE_APPEND);
    }
    
});