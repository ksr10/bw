<?php

namespace Bw\Api\Service;

use Zend\Http\Client;

class Connect
{
    public function connect($uri, $post = false, $params = null)
    {        
        $client = new Client($uri, array(            
            'adapter' => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible;)',
                CURLOPT_FRESH_CONNECT => 1,
                CURLOPT_FORBID_REUSE => 1,
                CURLOPT_TIMEOUT => 100,
                CURLOPT_SSL_VERIFYHOST => 2
            ),  
        ));
        
        if (!$post) {
            $client->setParameterGet($params);
        }
        
        $request = $client->getRequest();
        $response = $client->dispatch($request);
        
        /*$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");   
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);   
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);  
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        
        $response = curl_exec($ch);
        
        curl_close($ch);*/
        
        return $response;
    }
    
    protected function removeSpaces($string)
    {
        return trim(preg_replace('!\s+!smi', ' ', $string));
    }
}