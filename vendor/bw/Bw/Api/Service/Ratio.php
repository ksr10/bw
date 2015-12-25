<?php

namespace Bw\Api\Service;

use Bw\Api\Service\Marathonbet as MarathonbetService;

class Ratio
{
    const MARATHONBET_SITE = 'marathonbet';
    
    protected $sites = array(
        'marathonbet' => 'https://www.marathonbet.com'
    );
    
    public function pickupRatio()
    {
        foreach ($this->sites as $name => $uri) {
            switch ($name) {
                case self::MARATHONBET_SITE:
                    $service = new MarathonbetService();
                    break;
            }
            
            $service->dispatchRatio();
        }
    }
}