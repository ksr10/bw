<?php

namespace Bw\Api\Service;

use Bw\Api\Service\Marathonbet as MarathonbetService;
use Bw\Common\Db\Result as ResultDb;
use Bw\Api\Service\Ratio as RatioService;

class Result extends Base
{    
    const MARATHONBET_SITE = 'marathonbet';
    
    const RESULT_STATUS_NEW = 'new';
    
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
            $data = $service->getResultData();
            
            $this->saveSiteResults($data);
        }
    }
    
    protected function saveSiteResults($data)
    {
        $ratioService = new RatioService();
        
        foreach ($data as $event) {
            $ratio = $ratioService->getRatioByTeams($event['host_team'], $event['guest_team']);
            
            if ($ratio) {
                $event['ratio_id'] = $ratio['id'];
                $event['status'] = self::RESULT_STATUS_NEW;
                
                ResultDb::i()->saveResult($event);
            }
        }        
    }
    
    public function getResultByTeam($hostTeam, $guestTeam)
    {
        return  ResultDb::i()->getResultByTeam($hostTeam, $guestTeam);
    }
    
    public function deleteAllResults()
    {
        return  ResultDb::i()->deleteAllResults();
    }
}