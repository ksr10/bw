<?php

namespace Bw\Api\Service;

use Zend\Http\Client;

class Connect
{
    public function connect($uri, $post = false, $params = null)
    {        
        $client = new Client($uri, array(
            'timeout' => 600,
            'sslverifypeer' => false
        ));
        
        if (!$post) {
            $client->setParameterGet($params);
        }
        
        $request = $client->getRequest();
        
        $response = $client->dispatch($request);
        
        return $response;
    }
    
    protected function removeSpaces($string)
    {
        return trim(preg_replace('!\s+!smi', ' ', $string));
    }
}