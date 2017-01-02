<?php

use Bw\Api\Service\Applicant as ApplicantService;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
    $filename = getcwd() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'admin.html';
    $fileContent = '';
    
    if (file_exists($filename)) {
        $fileContent = file_get_contents($filename);
    }
    
    return $response->withStatus(200)
            ->withHeader('Content-Type', 'text/html')
            ->write($fileContent);
});

$app->post('/applicant/save', function (ServerRequestInterface $request, ResponseInterface $response, $args) use ($app) {
    $result = array();
    $statusCode = 200;
    
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
             
    try {
        $applicantService = new ApplicantService();
        $applicantService->saveApplicants($data);
        
        $result['status'] = 1;         
    } catch (\Exception $e) {
        $result['status'] = 0;
        $result['msg'] = $e->getMessage();        
    }    
        
    return $response->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($result, JSON_PRETTY_PRINT));
});

$app->post('/applicant/save-selected', function (ServerRequestInterface $request, ResponseInterface $response, $args) use ($app) {
    $result = array();
    $statusCode = 200;
    
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
             
    try {
        $applicantService = new ApplicantService();
        $applicantService->saveSelectedApplicants($data);
        
        $result['status'] = 1;         
    } catch (\Exception $e) {
        $result['status'] = 0;
        $result['msg'] = $e->getMessage();        
    }    
        
    return $response->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($result, JSON_PRETTY_PRINT));
});

$app->get('/applicant/list-new', function (ServerRequestInterface $request, ResponseInterface $response, $args) use ($app) {
    $result = array();
    $statusCode = 200;
                 
    try {
        $applicantService = new ApplicantService();
        $result['applicants'] = $applicantService->getNewApplicants();
        
        $result['status'] = 1;         
    } catch (\Exception $e) {
        $result['status'] = 0;
        $result['msg'] = $e->getMessage();        
    }    
        
    return $response->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($result, JSON_PRETTY_PRINT));
});

$app->get('/applicant/list-wait-result', function (ServerRequestInterface $request, ResponseInterface $response, $args) use ($app) {
    $result = array();
    $statusCode = 200;
                 
    try {
        $applicantService = new ApplicantService();
        $result['applicants'] = $applicantService->getWaitResultApplicants();
        
        $result['status'] = 1;         
    } catch (\Exception $e) {
        $result['status'] = 0;
        $result['msg'] = $e->getMessage();        
    }    
        
    return $response->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($result, JSON_PRETTY_PRINT));
});

$app->post('/applicant/save-results', function (ServerRequestInterface $request, ResponseInterface $response, $args) use ($app) {
    $result = array();
    $statusCode = 200;
    
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
             
    try {
        $applicantService = new ApplicantService();
        $applicantService->saveResults($data);
        
        $result['status'] = 1;         
    } catch (\Exception $e) {
        $result['status'] = 0;
        $result['msg'] = $e->getMessage();        
    }    
        
    return $response->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($result, JSON_PRETTY_PRINT));
});