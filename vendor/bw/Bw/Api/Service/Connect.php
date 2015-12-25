<?php

namespace Bw\Api\Service;

use Bw\Api\Http\Client;
use Curl\Curl;

class Connect
{
    public function connect($uri, $post = false, $params = null)
    {
        $curl = new Curl($uri);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1');
                
        $curl->get($uri);
        
        if (!$curl->error) {
            $response = $curl->response;
            //file_put_contents($GLOBALS['root_dir'].'/data/logs/logger.log', print_r($curl->response, 1 ) . "\n", FILE_APPEND);
        } else {
            throw new \Exception('Connection Error to ' . $uri);
        }
        
        $curl->close();
        echo '<pre>' . var_dump('test') . '</pre>'; die();
    }
}