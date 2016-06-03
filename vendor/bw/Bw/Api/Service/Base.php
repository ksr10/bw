<?php

namespace Bw\Api\Service;

class Base
{    
    protected $dbConfig;
    
    public function __construct() 
    {
        $this->dbConfig = $this->loadConfig();        
    }
    
    public function loadConfig()
    {
        $localConfig = array();
        
        if (file_exists($GLOBALS['root_dir'] . '/config/local.php')) {
            $localConfig = include($GLOBALS['root_dir'] . '/config/local.php');            
        }
        
        return $localConfig;
    }   
}