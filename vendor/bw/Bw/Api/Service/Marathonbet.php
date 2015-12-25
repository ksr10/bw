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
            /*$response = $this->connect($uri['uri'], false, $params);
            file_put_contents($GLOBALS['root_dir'].'/data/logs/example.html', $response);*/
            
            $response = file_get_contents($GLOBALS['root_dir'].'/data/logs/example.html');
            $dom = new \DOMDocument();
            $dom->loadHTML($response);
            
            $links = $dom->getElementsByTagName('a');
            file_put_contents($GLOBALS['root_dir'].'/data/logs/logger.log', print_r($links, 1 ) . "\n", FILE_APPEND);
        }        
    }
}