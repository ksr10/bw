<?php

namespace Bw\Api\Service;

use Bw\Api\Service\Marathonbet as MarathonbetService;
use Bw\Common\Db\Result as ResultDb;

class Result extends Base
{    
    const MARATHONBET_SITE = 'marathonbet';
    
    protected $sites = array(
        'marathonbet' => 'https://www.marathonbet.com'
    );
    
    public function __construct() 
    {
        parent::__construct();
        ResultDb::i()->setConfig($this->dbConfig);      
    }
    
    public function pickupResult()
    {        
        foreach ($this->sites as $name => $uri) {
            switch ($name) {
                case self::MARATHONBET_SITE:
                    $service = new MarathonbetService();
                    break;
            }
            
            $service->dispatchResult();
            //$data = $service->getResultData();
            
            //$this->saveSiteResult($data);
        }
    }
}