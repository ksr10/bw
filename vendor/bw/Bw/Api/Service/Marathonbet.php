<?php

namespace Bw\Api\Service;

use Sunra\PhpSimple\HtmlDomParser;

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
            file_put_contents($GLOBALS['root_dir'].'/data/logs/example.html', $response);
            $dom = HtmlDomParser::str_get_html($response);*/
            $dom = HtmlDomParser::file_get_html($GLOBALS['root_dir'].'/data/logs/example.html');
            echo '<pre>' . var_dump(get_class($dom)) . '</pre>'; die();
        }        
    }
}