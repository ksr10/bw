<?php

namespace Bw\Api\Service;


class Marathonbet extends Connect
{
    protected $uries = array(
       'base' => array(
           'uri' => 'https://www.marathonbet.com/en/popular/Football',
           'params' => array()
       )
   );
    
    public function dispatchRatio()
    {
        foreach ($this->uries as $key => $uri) {
            $params = $uri['params'];
            $response = $this->connect($uri['uri'], false, $params);
            
        }        
    }
}