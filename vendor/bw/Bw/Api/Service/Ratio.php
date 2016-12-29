<?php

namespace Bw\Api\Service;

use Bw\Api\Service\Marathonbet as MarathonbetService;
use Bw\Common\Db\Ratio as RatioDb;

class Ratio extends Base
{
    const MARATHONBET_SITE = 'marathonbet';
    const RATIO_TIME = ' +1 day';
    //const RATIO_TIME = ' +3 day';
    
    protected $sites = array(
        'marathonbet' => 'https://www.marathonbet.com'
    );
    
    public function __construct() 
    {
        parent::__construct();
        RatioDb::i()->setConfig($this->dbConfig);      
    }
       
    public function pickupRatio()
    {        
        foreach ($this->sites as $name => $uri) {
            switch ($name) {
                case self::MARATHONBET_SITE:
                    $service = new MarathonbetService();
                    break;
            }
            
            $service->dispatchRatio();
            $data = $service->getRatioData();
            
            $this->saveSiteRatio($data);
        }
    }
    
    public function saveSiteRatio($data)
    {        
        foreach ($data as $item) {
            if (empty($item['country']) || empty($item['league_name'])) {
                file_put_contents($GLOBALS['root_dir'].'/data/logs/logger.log', print_r($item, 1 ) . "\n", FILE_APPEND);
                throw new \Exception('Html structure has been changed!');                
            }
            
            $leagueId = RatioDb::i()->saveLeague($item);
                        
            if ($leagueId) {
                foreach ($item['events'] as $event) {
                    $eventDate = strtotime($event['event_date']);  
                    $now = date('Y-m-d H:i:s');
                    $limitDate = strtotime(date('Y-m-d H:i:s', strtotime($now . self::RATIO_TIME)));
                    
                    if ($eventDate < $limitDate) {                        
                        $ratioId = RatioDb::i()->saveRatio($event, $leagueId);
                    }                    
                }                
            }            
        }
    }
    
    public function getRatioForBet($from, $to)
    {
        $from = $from - 1;
        $to = $to - 1;
        
        return RatioDb::i()->getRatioForBet($from, $to);
    }
    
    public function getRatioByTeams($hostTeam, $guestTeam)
    {
        return RatioDb::i()->getRatioByTeams($hostTeam, $guestTeam);
    }
    
    public function deleteAllRatios()
    {
        return RatioDb::i()->deleteAllRatios();
    }
}